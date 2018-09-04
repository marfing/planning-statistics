<?php

namespace App\Controller;

use App\Entity\NetworkElement;
use App\Entity\StatisticaRete;
use App\Entity\NetworkElementsType;
use App\Form\NetworkElementType;
use App\Repository\NetworkElementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/network/element")
 */
class NetworkElementController extends Controller
{
    /**
     * @Route("/", name="network_element_index", methods="GET")
     */
    public function index(NetworkElementRepository $networkElementRepository): Response
    {
        return $this->render('network_element/index.html.twig', ['network_elements' => $networkElementRepository->findAll()]);
    }

    /**
     * @Route("/index/{name}", name="network_element_index_filter", methods="GET")
     */
    public function indexFilter($name): Response
    {
        $networkElements = $this->getDoctrine()->getRepository(NetworkElement::class)->findAll();
        foreach ( $networkElements as $key => $element){
            if($element->getNetworkElementsType()->getName() != $name){
                unset($networkElements[$key]);    
            }
        }
        return $this->render('network_element/index.html.twig', ['network_elements' => $networkElements]);
    }

    /**
     * @Route("/admin/new", name="network_element_new", methods="GET|POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function new(Request $request): Response
    {
        $networkElement = new NetworkElement();
        $form = $this->createForm(NetworkElementType::class, $networkElement);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if($request->request->get('save') && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($networkElement);
                $em->flush();
                return $this->redirectToRoute('network_element_index');
            } //l'alternativa può solo essere il cancel
            return $this->redirectToRoute('network_element_index');
        }

        return $this->render('network_element/new.html.twig', [
            'network_element' => $networkElement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="network_element_show", methods="GET")
     */
    public function show(NetworkElement $networkElement): Response
    {
        $this->parseCSV($networkElement->getId());
        return $this->render('network_element/show.html.twig', ['network_element' => $networkElement]);
    }

    /**
     * @Route("/admin/{id}/edit", name="network_element_edit", methods="GET|POST")
     * @Security("has_role('ROLE_ADMIN')")
     * */
    public function edit(Request $request, NetworkElement $networkElement): Response
    {
        $form = $this->createForm(NetworkElementType::class, $networkElement);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if($request->request->get('save') && $form->isValid()){
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('network_element_show', array(
                                                        'id' => $networkElement->getId()
                ));
            }//l'alternativa può solo essere il cancel
            return $this->redirectToRoute('network_element_index');
        }

        return $this->render('network_element/edit.html.twig', [
            'network_element' => $networkElement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="network_element_delete", methods="DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function delete(Request $request, NetworkElement $networkElement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$networkElement->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($networkElement);
            $em->flush();
        }
        return $this->redirectToRoute('network_element_index');
    }

    /**
     * @Route("/admin/delete/{id}", name="network_element_mydelete", methods="DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function mydelete($id): Response
    {
        $networkElement = $this->getDoctrine()->getRepository(NetworkElement::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($networkElement);
        $entityManager->flush();
        return new Response();
    }

    /**
     * @Route("/admin/delete/{id}/statistics", name="network_element_delete_stat")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteStatistics(Request $request, $id): Response
    {
        $networkElement = $this->getDoctrine()->getRepository(NetworkElement::class)->find($id);
        foreach( $networkElement->getStatisticheRete() as $statistica){
            $networkElement->removeStatisticheRete($statistica);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $referer = $request->headers->get('referer');   
        if($referer){
            return new RedirectResponse($referer);
        } else {
            return $this->redirectToRoute('network_element_index');
        }
    }

    /**
     * @Route("/graph/{id}", name="network_element_graph", methods="GET")
     */
    public function graph($id)
    {
        $networkElement = $this->getDoctrine()
        ->getRepository(NetworkElement::class)
        ->find($id);

        $statistics = $networkElement->getStatisticheRete();   

        return $this->render('/graph/graph.html.twig',[
            'statistics' => $statistics,
            'network_element' => $networkElement,
            'last_value' => $networkElement->getLastStatisticValue(), 
            'free_capacity'=> $networkElement->getFreeCapacity(),
            'free_percentage' => $networkElement->getFreePercentage()
        ]); 
    }

    public function parseCSV($id)
    {
        $networkElement = $this->getDoctrine()
                                ->getRepository(NetworkElement::class)
                                ->find($id);
        $path = $networkElement->getDirectoryStatistiche();
        $fileList = scandir($path);
        //marfi - serve adesso cercare tutti i file csv, raggrupparli per giorno e prendere sempre il valore maggiore da inserire nel db
        $dataValues = array();
        $wrongFileList = array();
        $newGraphValue = false;
        $csvExist = false; //usato solo per il report finale - true se esiste anche un solo file csv

        foreach ($fileList as $fileName){
            $dataAlreadyInArray = false;
            if(strpos($fileName,'.csv') != false){
                $csvExist = true;
                //iniziamo l'apertura del file csv per controllare se esiste la colonna dove andare a leggere il valore
                $row = 1;
                if (($handle = fopen("$path/$fileName","r")) !== FALSE) {
                    $valueIndex = 0; //indice che rappresenta la colonna dove è presente il valore da controllare  
                    if(( $fileRow = fgetcsv($handle, 1000, ",")) != FALSE){
                        //controllo che esista l'indice di colonna settato per il network element
                        if($valueIndex == 0){
                            $num = count($fileRow); //conta il numero di elementi nell'array $fileRow
                            for ($c=0; $c < $num; $c++) {
                                if($fileRow[$c]===$networkElement->getCsvCapacityTypeName()){
                                    $valueIndex=$c;                                        
                                }
                            }
                        }  //fine controllo per ricerca indice di colonna corretto
                        if($valueIndex != 0 && !$dataAlreadyInArray){ 
                            //ricerca della data a partire dal nome del file
                            $pieces = explode("_",$fileName);
                            foreach ($pieces as $piece){
                                if( strlen($piece) >= 12){
                                    list($year,$month,$day) = sscanf($piece,"%4d%2d%2d");
                                    list($yearString,$monthString,$dayString) = sscanf($piece,"%4s%2s%2s");
                                    //implementare qui il check della validità per anno, mese e giorno
                                    if( ($year >= 2000) && ($year <= 3000) && ($month>=1) && ($month<=12) && ($day>=1) && ($day<=31) ){
                                        // creiamo la stringa data che diventerà l'indice dell'array associativo data-valore
                                        $data = "$yearString-$monthString-$dayString";
                                        //trovare se esiste già nell'array dataValues, altrimenti si crea con valore 0                                        
                                        foreach ($dataValues as $dataArray => $valueArray){
                                            if($dataArray == $data){ $dataAlreadyInArray = true; }
                                        }
                                        if(!$dataAlreadyInArray){
                                            $dataValues[$data]=0;
                                        }
                                    }
                                }
                            }//fine scansione segmenti del file name separati da _ per trovare la data
                            //scandisce le righe e verifica se il nuovo valore trovato è maggiore di quello già esistente
                            while ( ($fileRow = fgetcsv($handle, 1000, ",")) !== FALSE) { //scandisco le righe
                                $num = count($fileRow); //conta il numero di elementi nell'array $fileRow
                                if($num > 0){ //mi assicuro che ci siano valori da cercare
                                    for ($c=0; $c < $num; $c++) {
                                        if(($valueIndex==$c)  
                                            && ($fileRow[$c] > $dataValues[$data])
                                            &&  is_numeric($fileRow[$c])
                                        ){
                                            $dataValues[$data]=$fileRow[$c]; 
                                            //echo("<p>Filename: $fileName - Valore: " .$fileRow[$c]."</p>");
                                        }
                                    }                                        
                                }
                            }//fine ricerva nuovo valore
                        }//fine gestione ricerva valori avendo trovato indice colonna valido  
                    }//fine gestione singola riga file csv valida
                    fclose($handle);
                } //fine gestione del file handler
            } else { $wrongFileList[] = $fileName;}//fine check csv file
        } //fine parsing singolo file
        $em = $this->getDoctrine()->getManager();
        foreach ($dataValues as $data => $value ){
            $alreadyExist = false;
            foreach( $networkElement->getStatisticheRete() as $oldStatistica){
                if($oldStatistica->getDataAsString() == $data){
                    $oldStatistica->setValore($value);
                    $alreadyExist = true;
                    $em->flush();
                }
            }
            //controllo cheanche il valore ottenuto sia coerente, non deve essere più del quadruplo del valore del giorno precedente
            if(!$alreadyExist && (($value/$networkElement->getLastStatisticValue()) < 4)) {
                $statistica = new StatisticaRete;
                $statistica->setDataFromString($data);
                $statistica->setValore($value);
                $networkElement->addStatisticheRete($statistica); //aggiunge anche networkElement a statisicaRete       
                $em->persist($statistica);  
                $newGraphValue = true;      
            }
            $em->flush();
        }
    }


    /**
     * @Route("/uploadcsv/{id}", name="network_element_upload_csv", methods="GET")
     */
    public function uploadCsv(Request $request, $id)
    {
        $this->parseCSV($id);
        $referer = $request->headers->get('referer');   
        if($referer){
            return new RedirectResponse($referer);
        } else {
            return $this->redirectToRoute('network_element_index');
        }
    }


    /**
     * @Route("/admin/backupcsv/{id}", name="network_element_backup_csv", methods="GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function backupCsv(Request $request, $id)
    {
        $networkElement = $this->getDoctrine()
            ->getRepository(NetworkElement::class)
            ->find($id);
        $path = $networkElement->getDirectoryStatistiche();
        //$path = '../statistiche/'. $networkElement->getDirectoryStatistiche();
        $fileList = scandir($path);
        $phpFileCounter=0;
        $wrongFileTypeCounter=0;
        foreach ($fileList as $fileName)    {
            if(strpos($fileName,'.csv') != false){
                rename("$path/$fileName","$path/backup/$fileName");
                $phpFileCounter++;
            } else {
                $wrongFileTypeCounter++;
            }
        }
        $referer = $request->headers->get('referer');   
        if($referer){
            return new RedirectResponse($referer);
        } else {
            return $this->redirectToRoute('network_element_index');
        }
    }

    /**
     * @Route("/admin/backupcsvdelete/{id}", name="network_element_backup_csv_delete", methods="GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function backupCsvDelete(Request $request, $id)
    {
        $networkElement = $this->getDoctrine()
        ->getRepository(NetworkElement::class)
        ->find($id);
        $path = $networkElement->getDirectoryStatistiche() . "/backup";
        $fileList = scandir($path);
        $phpFileCounter=0;
        $wrongFileTypeCounter=0;
        foreach ($fileList as $fileName) {
            if (file_exists($path."/".$fileName) && (strpos($fileName,'.csv') != false)) {
                unlink($path."/".$fileName);
                $phpFileCounter++;
            } else {
                $wrongFileTypeCounter++;
                // File not found.
            }
        }
        $referer = $request->headers->get('referer');   
        if($referer){
            return new RedirectResponse($referer);
        } else {
            return $this->redirectToRoute('network_element_index');
        }

    }

    /**
     * @Route("/admin/csvdelete/{id}", name="network_element_csv_delete", methods="GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function csvDelete(Request $request, $id)
    {
        $networkElement = $this->getDoctrine()
            ->getRepository(NetworkElement::class)
            ->find($id);
        $path = $networkElement->getDirectoryStatistiche();
        $fileList = scandir($path);
        $phpFileCounter=0;
        $wrongFileTypeCounter=0;
        foreach ($fileList as $fileName) {
            if (file_exists($path."/".$fileName) && (strpos($fileName,'.csv') != false)) {
                unlink($path."/".$fileName);
                $phpFileCounter++;
            } else {
                $wrongFileTypeCounter++;
                // File not found.
            }
        }
        $referer = $request->headers->get('referer');   
        if($referer){
            return new RedirectResponse($referer);
        } else {
            return $this->redirectToRoute('network_element_index');
        }

    }

    public function getElementsGraphTable(){
        $networkElements = $this->getDoctrine()->getRepository(NetworkElement::class)->findBy(
            ['dashboard' => true]
        );
        return $this->render('network_element/graph_table.html.twig', [
            'elements' => $networkElements
        ]);    
    }

    public function getElementsGraphTableJs(){
        $networkElements = $this->getDoctrine()->getRepository(NetworkElement::class)->findAll();
        return $this->render('network_element/graph_table_js.html.twig', [
            'elements' => $networkElements
        ]);
    }

    /**
     * @Route("/index/updateallcsv/", name="network_element_update_all", methods="GET")
     */
    public function updateAllCsv(Request $request){
        $networkElements = $this->getDoctrine()->getRepository(NetworkElement::class)->findAll();
        foreach ($networkElements as $networkElement) {
            $this->parseCSV($networkElement->getId());
        }
        $referer = $request->headers->get('referer');   
        if($referer){
            return new RedirectResponse($referer);
        } else {
            return $this->redirectToRoute('network_element_index');
        }
    }

}

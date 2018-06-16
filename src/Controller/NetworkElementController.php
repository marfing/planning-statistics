<?php

namespace App\Controller;

use App\Entity\NetworkElement;
use App\Entity\StatisticaRete;
use App\Form\NetworkElementType;
use App\Repository\NetworkElementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/new", name="network_element_new", methods="GET|POST")
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
        return $this->render('network_element/show.html.twig', ['network_element' => $networkElement]);
    }

    /**
     * @Route("/{id}/edit", name="network_element_edit", methods="GET|POST")
     */
    public function edit(Request $request, NetworkElement $networkElement): Response
    {
        $form = $this->createForm(NetworkElementType::class, $networkElement);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if($request->request->get('save') && $form->isValid()){
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('network_element_index');
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
     * @Route("/delete/{id}", name="network_element_mydelete", methods="DELETE")
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
     * @Route("/delete/{id}/statistics", name="network_element_delete_stat")
     */
    public function deleteStatistics($id): Response
    {
        $networkElement = $this->getDoctrine()->getRepository(NetworkElement::class)->find($id);
        foreach( $networkElement->getStatisticheRete() as $statistica){
            $networkElement->removeStatisticheRete($statistica);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirectToRoute('network_element_index');
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
            'element' => $networkElement,
        ]); 
    }

    /**
     * @Route("/uploadcsv/{id}", name="network_element_upload_csv", methods="GET")
     */
    public function uploadCsv($id)
    {
        $networkElement = $this->getDoctrine()
        ->getRepository(NetworkElement::class)
        ->find($id);
        $path = '../statistiche/'. $networkElement->getDirectoryStatistiche();
        $fileList = scandir($path);
        //marfi - serve adesso cercare tutti i file csv, raggrupparli per giorno e prendere sempre il valore maggiore da inserire nel db
        $dataValues = array();
        $wrongFileList = array();
        $csvExist = false;
        $newGraphValue = false;
        foreach ($fileList as $fileName){
            if(strpos($fileName,'.csv') != false){
                $csvExist = true;
                //iniziamo l'apertura del file csv per controllare se esiste la colonna dove andare a leggere il valore
                $row = 1;
                if (($handle = fopen("$path/$fileName","r")) !== FALSE) {
                    $valueIndex = 0; //indice che rappresenta la colonna dove è presente il valore da controllare  
                    $fileRow = fgetcsv($handle, 1000, ",");
                    //controllo che esista l'indice di colonna settato per il network element
                    $num = count($fileRow); //conta il numero di elementi nell'array $fileRow
                    for ($c=0; $c < $num; $c++) {
                        if($fileRow[$c]===$networkElement->getCsvCapacityTypeName()){
                            $valueIndex=$c;                                        
                        }
                    }
                    if($valueIndex != 0){ 
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
                                    $dataAlreadyInArray = false;
                                    foreach ($dataValues as $dataArray => $valueArray){
                                        if($dataArray == $data){ $dataAlreadyInArray = true; }
                                    }
                                    if(!$dataAlreadyInArray){$dataValues[$data]=0;}
                                }
                            }
                        }//fine scansione segnmenti del file name separati da _ per trovare la data
        
                        while ( ($fileRow = fgetcsv($handle, 1000, ",")) !== FALSE) { //scandisco le righe
                            $num = count($fileRow); //conta il numero di elementi nell'array $fileRow
                            for ($c=0; $c < $num; $c++) {
                                if(($valueIndex==$c) && ($fileRow[$c] > $dataValues[$data])){
                                    $dataValues[$data]=$fileRow[$c];     
                                }
                            }                                    

                        }
                    }
                    fclose($handle);
                } //fine gestione contenuto del file
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
            if(!$alreadyExist){
                $statistica = new StatisticaRete;
                $statistica->setDataFromString($data);
                $statistica->setValore($value);
                $networkElement->addStatisticheRete($statistica); //aggiunge anche networkElement a statisicaRete       
                $em->persist($statistica);  
                $newGraphValue = true;      
            }
            $em->flush();
        }
        return $this->render('network_element/csv_loaded.html.twig',array(
            'dataValues' => $dataValues,
            'csvExist' => $csvExist,
            'path' => $path,
            'newValue' => $newGraphValue,
            'wrongFileList' => $wrongFileList));
    }


    /**
     * @Route("/backupcsv/{id}", name="network_element_backup_csv", methods="GET")
     */
    public function backupCsv($id)
    {
        $networkElement = $this->getDoctrine()
        ->getRepository(NetworkElement::class)
        ->find($id);
        $path = '../statistiche/'. $networkElement->getDirectoryStatistiche();
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
        return $this->render('network_element/csv_backup_report.html.twig',
                                array(
                                    'phpFiles' => $phpFileCounter,
                                    'wrongFiles' => $wrongFileTypeCounter));
    }

}

<?php

namespace App\Controller;

use App\Entity\Copertura;
use App\Entity\Comune;

use App\Form\CoperturaType;
use App\Repository\CoperturaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


/**
 * @Route("/copertura")
 */
class CoperturaController extends AbstractController
{
    /**
     * @Route("/", name="copertura_index", methods="GET")
     */
    public function index(CoperturaRepository $coperturaRepository): Response
    {
        $errors=array();
        return $this->render('copertura/index.html.twig', [
            'coperture' => $coperturaRepository->findAll(),
            'errori' => $errors,
        ]);
    }

    /**
     * @Route("/new", name="copertura_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $copertura = new Copertura();
        $form = $this->createForm(CoperturaType::class, $copertura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($copertura);
            $em->flush();

            return $this->redirectToRoute('copertura_index');
        }

        return $this->render('copertura/new.html.twig', [
            'copertura' => $copertura,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="copertura_show", methods="GET")
     */
    public function show(Copertura $copertura): Response
    {
        return $this->render('copertura/show.html.twig', ['copertura' => $copertura]);
    }

    /**
     * @Route("/{id}/edit", name="copertura_edit", methods="GET|POST")
     */
    public function edit(Request $request, Copertura $copertura): Response
    {
        $form = $this->createForm(CoperturaType::class, $copertura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('copertura_edit', ['id' => $copertura->getId()]);
        }

        return $this->render('copertura/edit.html.twig', [
            'copertura' => $copertura,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="copertura_delete", methods="DELETE")
     */
    public function delete(Request $request, Copertura $copertura): Response
    {
        if ($this->isCsrfTokenValid('delete'.$copertura->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($copertura);
            $em->flush();
        }

        return $this->redirectToRoute('copertura_index');
    }


    /**
     * @Route("/delete/all", name="copertura_delete_all", methods="GET")
     */
    public function deleteAll(Request $request, CoperturaRepository $coperturaRepository): Response
    {
        
        $listCoperture=$coperturaRepository->findAll();
        foreach($listCoperture as $copertura){
            $copertura->getComune()->removeCoperture($copertura);
            $em = $this->getDoctrine()->getManager();
            $em->persist($copertura->getComune());            
            $em->remove($copertura);
        }
        $em->flush();
        return $this->redirectToRoute('copertura_index');
    }


        /**
     * @Route("/csv/import_eof", name="copertura_import_csv_eof", methods="GET|POST")
     */
    public function importCsv(Request $request, CoperturaRepository $coperturaRepository): Response
    {
        
        $dataPath = array('pathcsv' => '');
        $form = $this->createFormBuilder($dataPath)
            ->add('pathcsv', TextType::class, array(
                'label' => 'Path file csv coperture EOF',
            ))
            ->add('carica', SubmitType::class,array('label'=>'Carica'))
            ->getForm();
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $path = $data['pathcsv'];
            $valueIndex = 0;
            $firstRow = true;
            $errors=array();
            $em = $this->getDoctrine()->getManager();
            if (($handle = @fopen($path,"r")) !== FALSE){
                while(( $fileRow = fgetcsv($handle, 1000, ";")) != FALSE){
                    if($firstRow){
                        //setto gli indici per gli attributi del comune
                        $idScalaIndex=array_search('ID_SCALA',$fileRow);
                        if($idScalaIndex == false){
                            $errors[]="Index ID_SCALA not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $regioneIndex=array_search('REGIONE',$fileRow);
                        if($regioneIndex == false){
                            $errors[]="Index REGIONE not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $provinciaIndex=array_search('PROVINCIA',$fileRow);
                        if($provinciaIndex == false){
                            $errors[]="Index PROVINCIA not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $codiceComuneIndex=array_search('COMUNE',$fileRow);
                        if($codiceComuneIndex == false){
                            $errors[]="Index COMUNE not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $frazioneIndex=array_search('FRAZIONE',$fileRow);
                        if($frazioneIndex == false){
                            $errors[]="Index FRAZIONE not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $particellaTopIndex=array_search('PARTICELLA_TOP',$fileRow);
                        if($particellaTopIndex == false){
                            $errors[]="Index PARTICELLA_TOP not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $indirizzoIndex=array_search('INDIRIZZO',$fileRow);
                        if($indirizzoIndex == false){
                            $errors[]="Index INDIRIZZO not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $civicoIndex=array_search('CIVICO',$fileRow);
                        if($civicoIndex == false){
                            $errors[]="Index CIVICO not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $scalaPalazzinaIndex=array_search('SCALA_PALAZZINA',$fileRow);
                        if($scalaPalazzinaIndex == false){
                            $errors[]="Index SCALA_PALAZZINA not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $codiceViaIndex=array_search('CODICE_VIA',$fileRow);
                        if($codiceViaIndex == false){
                            $errors[]="Index CODICE_VIA not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $idBuildingIndex=array_search('ID_BUILDING',$fileRow);
                        if($idBuildingIndex == false){
                            $errors[]="Index ID_BUILDING not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $coordinateBuildingIndex=array_search('COORDINATE_BUILDING',$fileRow);
                        if($coordinateBuildingIndex == false){
                            $errors[]="Index COORDINATE_BUILDING not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $popIndex=array_search('POP',$fileRow);
                        if($popIndex == false){
                            $errors[]="Index POP not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $totaleUIIndex=array_search('TOTALE_UI',$fileRow);
                        if($totaleUIIndex == false){
                            $errors[]="Index TOTALE_UI not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $statoBuildingIndex=array_search('STATO_BUILDING',$fileRow);
                        if($statoBuildingIndex == false){
                            $errors[]="Index STATO_BUILDING not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $statoScalaPalazzinaIndex=array_search('STATO_SCALA_PALAZZINA',$fileRow);
                        if($statoScalaPalazzinaIndex == false){
                            $errors[]="Index STATO_SCALA_PALAZZINA not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $dataRFCIndicativaIndex=array_search('DATA_RFC_INDICATIVA',$fileRow);
                        if($dataRFCIndicativaIndex == false){
                            $errors[]="Index DATA_RFC_INDICATIVA not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $dataRFCEffettivaIndex=array_search('DATA_RFC_EFFETTIVA',$fileRow);
                        if($dataRFCEffettivaIndex == false){
                            $errors[]="Index DATA_RFC_EFFETTIVA not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $dataRFAIndicativaIndex=array_search('DATA_RFA_INDICATIVA',$fileRow);
                        if($dataRFAIndicativaIndex == false){
                            $errors[]="Index DATA_RFA_INDICATIVA not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $dataRFAEffettivaIndex=array_search('DATA_RFA_EFFETTIVA',$fileRow);
                        if($dataRFAEffettivaIndex == false){
                            $errors[]="Index DATA_RFA_EFFETTIVA not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $dataUltimaModificaRecordIndex=array_search('DATA_ULTIMA_MODIFICA_RECORD',$fileRow);
                        if($dataUltimaModificaRecordIndex == false){
                            $errors[]="Index DATA_ULTIMA_MODIFICA_RECORD not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $dataUltimaModificaStatoBuildingIndex=array_search('DATA_ULTIMA_VARIAZIONE_STATO_BUILDING',$fileRow);
                        if($dataUltimaModificaStatoBuildingIndex == false){
                            $errors[]="Index DATA_ULTIMA_VARIAZIONE_STATO_BUILDING not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $dataUltimaVariazioneStatoScalaPalazzinaIndex=array_search('DATA_ULTIMA_VARIAZIONE_STATO_SCALA_PALAZZINA',$fileRow);
                        if($dataUltimaVariazioneStatoScalaPalazzinaIndex == false){
                            $errors[]="Index DATA_ULTIMA_VARIAZIONE_STATO_SCALA_PALAZZINA not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $firstRow=false;
                    } else {
                        $copertura = new Copertura();
                        $copertura->setIdScala($fileRow[$idScalaIndex]);                
                        $copertura->setRegione($fileRow[$regioneIndex]);                
                        $copertura->setProvincia($fileRow[$provinciaIndex]);
                        $copertura->setCodiceComune($fileRow[$codiceComuneIndex]);                
                        $copertura->setFrazione($fileRow[$frazioneIndex]);                
                        $copertura->setParticellaTop($fileRow[$particellaTopIndex]);                
                        $copertura->setIndirizzo($fileRow[$indirizzoIndex]);                
                        $copertura->setCivico($fileRow[$civicoIndex]);                
                        $copertura->setScalaPalazzina($fileRow[$scalaPalazzinaIndex]);                
                        $copertura->setCodiceVia($fileRow[$codiceViaIndex]);                
                        $copertura->setIdBuilding($fileRow[$idBuildingIndex]);                
                        $copertura->setCoordinateBuilding($fileRow[$coordinateBuildingIndex]);                
                        $copertura->setPop($fileRow[$popIndex]);                
                        $copertura->setTotaleUI($fileRow[$totaleUIIndex]);                
                        $copertura->setStatoBuilding($fileRow[$statoBuildingIndex]);                
                        $copertura->setStatoScalaPalazzina($fileRow[$statoScalaPalazzinaIndex]);                
                        $dataFormat='d/m/Y H.i.s';
                        if(!empty($fileRow[$dataRFCIndicativaIndex])){
                            $copertura->setDatatRFCIndicativa(\DateTime::createFromFormat($dataFormat,$fileRow[$dataRFCIndicativaIndex]));                
                        }
                        if(!empty($fileRow[$dataRFCEffettivaIndex])){
                            $copertura->setDataRFCEffettiva(\DateTime::createFromFormat($dataFormat,$fileRow[$dataRFCEffettivaIndex]));                
                        }
                        if(!empty($fileRow[$dataRFAIndicativaIndex])){
                            $copertura->setDataRFAIndicativa(\DateTime::createFromFormat($dataFormat,$fileRow[$dataRFAIndicativaIndex]));                
                        }
                        if(!empty($fileRow[$dataRFAEffettivaIndex])){
                            $copertura->setDataRFAEffettiva(\DateTime::createFromFormat($dataFormat,$fileRow[$dataRFAEffettivaIndex]));                
                        }
                        if(!empty($fileRow[$dataUltimaModificaRecordIndex])){
                            $copertura->setDataUltimaModificaRecord(\DateTime::createFromFormat($dataFormat,$fileRow[$dataUltimaModificaRecordIndex]));                
                        }
                        if(!empty($fileRow[$dataUltimaModificaStatoBuildingIndex])){
                            $copertura->setDataUltimaModificaStatoBuilding(\DateTime::createFromFormat($dataFormat,$fileRow[$dataUltimaModificaStatoBuildingIndex]));                
                        }
                        if(!empty($fileRow[$dataUltimaVariazioneStatoScalaPalazzinaIndex])){
                            $copertura->setDataUltimaVariazioneStatoScalaPalazzina(\DateTime::createFromFormat($dataFormat,$fileRow[$dataUltimaVariazioneStatoScalaPalazzinaIndex]));                
                        }
                        $comune = $this->getDoctrine()
                                    ->getRepository(Comune::class)
                                    ->findOneBy(['codiceComune' => $copertura->getCodiceComune()]);
                        $comune->addCoperture($copertura);
                        $em->persist($copertura);
                        $em->persist($comune);
                    }
                }//fine parsing linee file csv
                $em->flush();
            } else {
                $errors[] = "file: ".$path." not found";
            }
            return $this->render('copertura/index.html.twig', [
                'coperture' => $coperturaRepository->findAll(),
                'errori' => $errors,
                ]);
        }
        return $this->render('copertura/importcsv_copertura_eof.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}

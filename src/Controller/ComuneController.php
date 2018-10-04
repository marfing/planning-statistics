<?php

namespace App\Controller;

use App\Entity\Comune;
use App\Form\ComuneType;
use App\Repository\ComuneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/comune")
 */
class ComuneController extends AbstractController
{
    /**
     * @Route("/", name="comune_index", methods="GET")
     */
    public function index(ComuneRepository $comuneRepository): Response
    {
        $errors = array();
        return $this->render('comune/index.html.twig', [
            'comuni' => $comuneRepository->findAll(),
            'errors' => $errors,
            ]);
    }

    /**
     * @Route("/new", name="comune_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $comune = new Comune();
        $form = $this->createForm(ComuneType::class, $comune);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comune);
            $em->flush();

            return $this->redirectToRoute('comune_index');
        }

        return $this->render('comune/new.html.twig', [
            'comune' => $comune,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comune_show", methods="GET")
     */
    public function show(Comune $comune): Response
    {
        return $this->render('comune/show.html.twig', ['comune' => $comune]);
    }

    /**
     * @Route("/{id}/edit", name="comune_edit", methods="GET|POST")
     */
    public function edit(Request $request, Comune $comune): Response
    {
        $form = $this->createForm(ComuneType::class, $comune);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comune_edit', ['id' => $comune->getId()]);
        }

        return $this->render('comune/edit.html.twig', [
            'comune' => $comune,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comune_delete", methods="DELETE")
     */
    public function delete(Request $request, Comune $comune): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comune->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comune);
            $em->flush();
        }

        return $this->redirectToRoute('comune_index');
    }

    
    /**
     * @Route("/delete/all", name="comune_delete_all", methods="GET")
     */
    public function deleteAll(Request $request, ComuneRepository $comuneRepository): Response
    {
        
        $listComuni=$comuneRepository->findAll();
        foreach($listComuni as $comune){
            $em = $this->getDoctrine()->getManager();
            $em->remove($comune);
        }
        $em->flush();
        return $this->redirectToRoute('comune_index');
    }

    
    /**
     * @Route("/csv/import", name="comune_import_csv", methods="GET|POST")
     */
    public function importCsv(Request $request, ComuneRepository $comuneRepository): Response
    {
        
        $dataPath = array('pathcsv' => '');
        $form = $this->createFormBuilder($dataPath)
            ->add('pathcsv', TextType::class, array(
                'label' => 'Path file csv dei comuni',
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
                        $denominazioneIndex=array_search('Denominazione in italiano',$fileRow);
                        if($denominazioneIndex == false){
                            $errors[]="Index Denominazione in italiano not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $codiceComuneIndex=array_search('Codice Comune formato alfanumerico',$fileRow);
                        if($codiceComuneIndex == false){
                            $errors[]="Index Codice Comune formato alfanumerico not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $codiceCittaMetropolitanaIndex=array_search('Codice Città Metropolitana',$fileRow);
                        if($codiceCittaMetropolitanaIndex == false){
                            $errors[]="Index Codice Città Metropolitana not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $denominazioneCittaMetropolitanaIndex=array_search('Denominazione Città metropolitana',$fileRow);
                        if($denominazioneCittaMetropolitanaIndex == false){
                            $errors[]="Index Denominazione Città metropolitana not in csv file - are you sure it is UTF-8 encoded?";
                        }
                        $firstRow=false;
                    } else {
                        $comune = new Comune();
                        $comune->setDenominazione($fileRow[$denominazioneIndex]);                
                        $comune->setCodiceComune($fileRow[$codiceComuneIndex]);                
                        $comune->setCodiceCittaMetropolitana($fileRow[$codiceCittaMetropolitanaIndex]);
                        $comune->setDenominazioneCittaMetropolitana($fileRow[$denominazioneCittaMetropolitanaIndex]);                
                        $em->persist($comune);
                    }
                }
                $em->flush();
            } else {
                $errors[] = "file: ".$path." not found";
            }
            return $this->render('comune/index.html.twig', [
                'comuni' => $comuneRepository->findAll(),
                'errors' => $errors,
                ]);
        }
    return $this->render('comune/importcsv.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}

<?php

namespace App\Controller;

use App\Entity\NetworkElement;
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
                return $this->redirectToRoute('network_element_edit', ['id' => $networkElement->getId()]);
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
     * @Route("/graph/{id}", name="network_element_graph", methods="GET")
     */
    public function graph($id)
    {
        $networkElement = $this->getDoctrine()
        ->getRepository(NetworkElement::class)
        ->find($id);

        $statistics = $networkElement->getStatisticheRete();   

        return $this->render('/graph/graph.html.twig');
    }

}

<?php

namespace App\Controller;

use App\Entity\NetworkElementsType;
use App\Form\NetworkElementsType1Type;
use App\Repository\NetworkElementsTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/network/element/type")
 */
class NetworkElementsTypeController extends Controller
{
    /**
     * @Route("/", name="network_elements_type_index", methods="GET")
     */
    public function index(NetworkElementsTypeRepository $networkElementsTypeRepository): Response
    {
        return $this->render('network_elements_type/index.html.twig', ['network_elements_types' => $networkElementsTypeRepository->findAll()]);
    }

    /**
     * @Route("/new", name="network_elements_type_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $networkElementsType = new NetworkElementsType();
        $form = $this->createForm(NetworkElementsType1Type::class, $networkElementsType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($networkElementsType);
            $em->flush();

            return $this->redirectToRoute('network_elements_type_index');
        }

        return $this->render('network_elements_type/new.html.twig', [
            'network_element_type' => $networkElementsType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="network_elements_type_show", methods="GET")
     */
    public function show(NetworkElementsType $networkElementsType): Response
    {
        return $this->render('network_elements_type/show.html.twig', ['network_elements_type' => $networkElementsType]);
    }

    /**
     * @Route("/{id}/edit", name="network_elements_type_edit", methods="GET|POST")
     */
    public function edit(Request $request, NetworkElementsType $networkElementsType): Response
    {
        $form = $this->createForm(NetworkElementsType1Type::class, $networkElementsType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('network_elements_type_edit', ['id' => $networkElementsType->getId()]);
        }

        return $this->render('network_elements_type/edit.html.twig', [
            'network_elements_type' => $networkElementsType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="network_elements_type_delete", methods="DELETE")
     */
    public function delete(Request $request, NetworkElementsType $networkElementsType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$networkElementsType->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($networkElementsType);
            $em->flush();
        }

        return $this->redirectToRoute('network_elements_type_index');
    }
}

<?php

namespace App\Controller;

use App\Entity\Vendor;
use App\Form\VendorType;
use App\Repository\VendorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/vendor")
 */
class VendorController extends Controller
{
    /**
     * @Route("/", name="vendor_index", methods="GET")
     */
    public function index(VendorRepository $vendorRepository): Response
    {
        return $this->render('vendor/index.html.twig', ['vendors' => $vendorRepository->findAll()]);
    }

    /**
     * @Route("/new", name="vendor_new", methods="GET|POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function new(Request $request): Response
    {
        $vendor = new Vendor();
        $form = $this->createForm(VendorType::class, $vendor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vendor);
            $em->flush();

            return $this->redirectToRoute('vendor_index');
        }

        return $this->render('vendor/new.html.twig', [
            'vendor' => $vendor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="vendor_show", methods="GET")
     */
    public function show(Vendor $vendor): Response
    {
        return $this->render('vendor/show.html.twig', ['vendor' => $vendor]);
    }

    /**
     * @Route("/{id}/edit", name="vendor_edit", methods="GET|POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function edit(Request $request, Vendor $vendor): Response
    {
        $form = $this->createForm(VendorType::class, $vendor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vendor_edit', ['id' => $vendor->getId()]);
        }

        return $this->render('vendor/edit.html.twig', [
            'vendor' => $vendor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="vendor_delete", methods="DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function delete(Request $request, Vendor $vendor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vendor->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($vendor);
            $em->flush();
        }

        return $this->redirectToRoute('vendor_index');
    }
}

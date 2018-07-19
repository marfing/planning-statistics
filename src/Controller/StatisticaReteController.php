<?php

namespace App\Controller;

use App\Entity\StatisticaRete;
use App\Form\StatisticaReteType;
use App\Repository\StatisticaReteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/statistica/rete")
 */
class StatisticaReteController extends Controller
{
    /**
     * @Route("/", name="statistica_rete_index", methods="GET")
     */
    public function index(StatisticaReteRepository $statisticaReteRepository): Response
    {
        return $this->render('statistica_rete/index.html.twig', ['statistica_retes' => $statisticaReteRepository->findAll()]);
    }

    /**
     * @Route("/index/{id}", name="statistica_rete_filtered_index", methods="GET")
     */
    public function filteredIndex(StatisticaReteRepository $statisticaReteRepository, $id): Response
    {
        return $this->render('statistica_rete/index.html.twig', ['statistica_retes' => $statisticaReteRepository->findByNetworkElementId($id)]);
    }

    /**
     * @Route("/new", name="statistica_rete_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $statisticaRete = new StatisticaRete();
        $form = $this->createForm(StatisticaReteType::class, $statisticaRete);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if($request->request->get('save') && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($statisticaRete);
                $em->flush();
                return $this->redirectToRoute('statistica_rete_index');
            }//l'alternativa può solo essere il cancel
            return $this->redirectToRoute('statistica_rete_index');
        }

        return $this->render('statistica_rete/new.html.twig', [
            'statistica_rete' => $statisticaRete,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="statistica_rete_show", methods="GET")
     */
    public function show(StatisticaRete $statisticaRete): Response
    {
        return $this->render('statistica_rete/show.html.twig', ['statistica_rete' => $statisticaRete]);
    }

    /**
     * @Route("/{id}/edit", name="statistica_rete_edit", methods="GET|POST")
     */
    public function edit(Request $request, StatisticaRete $statisticaRete): Response
    {
        $form = $this->createForm(StatisticaReteType::class, $statisticaRete);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if($request->request->get('save') && $form->isValid()){
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('statistica_rete_edit', ['id' => $statisticaRete->getId()]);
            }//l'alternativa può solo essere il cancel
            return $this->redirectToRoute('statistica_rete_index');
        }

        return $this->render('statistica_rete/edit.html.twig', [
            'statistica_rete' => $statisticaRete,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="statistica_rete_delete", methods="DELETE")
     */
    public function delete(Request $request, StatisticaRete $statisticaRete): Response
    {
        if ($this->isCsrfTokenValid('delete'.$statisticaRete->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($statisticaRete);
            $em->flush();
        }
        return $this->redirectToRoute('statistica_rete_index');
    }

    /**
     * @Route("/delete/{id}", name="statistica_rete_mydelete", methods="DELETE")
     */
    public function mydelete($id): Response
    {
        $statisticaRete = $this->getDoctrine()->getRepository(StatisticaRete::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($statisticaRete);
        $entityManager->flush();
        return new Response();
    }
}

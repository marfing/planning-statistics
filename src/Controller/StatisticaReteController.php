<?php

namespace App\Controller;

use App\Entity\StatisticaRete;
use App\Form\StatisticaReteType;
use App\Repository\StatisticaReteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

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
     * @Route("/new", name="statistica_rete_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $statisticaRete = new StatisticaRete();

        $form = $this->createFormBuilder($statisticaRete)
            ->add('valore', IntegerType::class, array(
                'attr' => array('class' => 'form-control'),
                'required' => true))
            ->add('nome_valore', ChoiceType::class, array(
                'choices' => array(
                    'Max Calls' => 'calls',
                    'Max Users' => 'users'),
                'attr' => array(
                    'class' => 'form-control')))
            ->add('data', DateType::class, array(
                'widget' => 'choice',
                'attr' => array(
                    'class' => 'form-control')))
            ->add('elemento_rete', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control'
                )
            ))->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($statisticaRete);
            $em->flush();

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
     * @Route("/edit/{id}", name="statistica_rete_edit", methods="GET|POST")
     */
    public function edit(Request $request, StatisticaRete $statisticaRete): Response
    {
        $form = $this->createFormBuilder($statisticaRete)
            ->add('valore', IntegerType::class, array(
                'attr' => array('class' => 'form-control'),
                'required' => true))
            ->add('nome_valore', ChoiceType::class, array(
                'choices' => array(
                    'Max Calls' => 'calls',
                    'Max Users' => 'users'),
                'attr' => array(
                    'class' => 'form-control')))
            ->add('data', DateType::class, array(
                'widget' => 'choice',
                'attr' => array(
                    'class' => 'form-control')))
            ->add('elemento_rete', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control'
                )
            ))->getForm();
            
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('statistica_rete_edit', ['id' => $statisticaRete->getId()]);
        }

        return $this->render('statistica_rete/edit.html.twig', [
            'statistica_rete' => $statisticaRete,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="statistica_rete_delete", methods="DELETE")
     */
    public function delete($id): Response
    {
        dump($id);

        $statisticaRete = $this->getDoctrine()->getRepository(StatisticaRete::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($statisticaRete);
        $entityManager->flush();
        
        return new Response();
    }
}

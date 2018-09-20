<?php

namespace App\Controller;

use App\Entity\TrafficReport;
use App\Form\TrafficReportType;
use App\Repository\TrafficReportRepository;
use App\Entity\DateInterval;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * @Route("/traffic/report")
 */
class TrafficReportController extends Controller
{
    /**
     * @Route("/", name="traffic_report_index", methods="GET")
     */
    public function index(TrafficReportRepository $trafficReportRepository): Response
    {
        $dateInterval = new DateInterval;
        $dateInterval->setStartDate(new \DateTime('now'));
        $dateInterval->setEndDate(new \DateTime('now'));

        $form = $this->createFormBuilder($dateInterval)
		    ->setAction($this->generateUrl('traffic_report_timeinterval_selected_index'))        
		    ->setMethod('GET')
		    ->add('startDate', DateTimeType::class, array(
			    'label' => 'Start Date',
                'html5' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
		    ))
            ->add('endDate', DateTimeType::class, array(
			    'label' => 'End Date',
			    'mapped' => 'false',
                'html5' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
            ))
            ->add('aggregate', CheckboxType::class, array(
                'label' => 'Aggrega',
                'required' => false,
                'data' => true,
            ))
            ->add('save', SubmitType::class, array('label' => 'Date Filter'))
            ->getForm();

        return $this->render('traffic_report/index.html.twig', array(
            'traffic_reports' => $trafficReportRepository->findAll(),
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/selected/{routerInIP}", name="traffic_report_selected_index", methods="GET")
     */
    public function selectedIndex(TrafficReportRepository $trafficReportRepository, string $routerInIP): Response
    {        
        return $this->render('traffic_report/index.html.twig', ['traffic_reports' => $trafficReportRepository->findBy(['routerInIP' => $routerInIP])]);
    }

    /**
     * @Route("/timeintervalselected/index", name="traffic_report_timeinterval_selected_index", methods="GET")
     */
    public function selectedTimeIntervalIndex(Request $request, TrafficReportRepository $trafficReportRepository): Response
    {
        $dateInterval = new DateInterval;

        $form = $this->createFormBuilder($dateInterval)
		    ->setAction($this->generateUrl('traffic_report_timeinterval_selected_index'))        
		    ->setMethod('GET')
		    ->add('startDate', DateTimeType::class, array(
			    'label' => 'Start Date',
                'html5' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
		    ))
            ->add('endDate', DateTimeType::class, array(
			    'label' => 'End Date',
                'html5' => 'true',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
            ))
            ->add('aggregate', CheckboxType::class, array(
                'label' => 'Aggrega',
                'required' => false,
                'data' => true,
            ))
            ->add('save', SubmitType::class, array('label' => 'Date Filter'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $dateInterval = $form->getData();
            $startTime = $dateInterval->getStartDate();
            $endTime = $dateInterval->getEndDate();
            $aggregate = $dateInterval->getAggregate();

            if(!$aggregate)
            {
                return $this->render('traffic_report/index.html.twig', [
                'traffic_reports' => $trafficReportRepository->findByTimeInterval($startTime, $endTime),
                'form' => $form->createView(),
                ]);
            } else {
                /*sviluppare logica di aggregazione
                    1) raggruppa per routerIn - routerOut
                    2) somma i valori di bw e dividi per il numero di campioni (date diverse)
                    3) restituisci tabella aggregata (array) - da capire come passare un array di trafficReport temporanei */
            }
        }
        return $this->render('traffic_report/index.html.twig', array(
            'traffic_reports' => $trafficReportRepository->findAll(),
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/new", name="traffic_report_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $trafficReport = new TrafficReport();
        $form = $this->createForm(TrafficReportType::class, $trafficReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($trafficReport);
            $em->flush();

            return $this->redirectToRoute('traffic_report_index');
        }

        return $this->render('traffic_report/new.html.twig', [
            'traffic_report' => $trafficReport,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="traffic_report_show", methods="GET")
     */
    public function show(TrafficReport $trafficReport): Response
    {
        return $this->render('traffic_report/show.html.twig', ['traffic_report' => $trafficReport]);
    }

    /**
     * @Route("/{id}/edit", name="traffic_report_edit", methods="GET|POST")
     */
    public function edit(Request $request, TrafficReport $trafficReport): Response
    {
        $form = $this->createForm(TrafficReportType::class, $trafficReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('traffic_report_edit', ['id' => $trafficReport->getId()]);
        }

        return $this->render('traffic_report/edit.html.twig', [
            'traffic_report' => $trafficReport,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="traffic_report_delete", methods="DELETE")
     */
    public function delete(Request $request, TrafficReport $trafficReport): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trafficReport->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($trafficReport);
            $em->flush();
        }
        return $this->redirectToRoute('traffic_report_index');
    }
}

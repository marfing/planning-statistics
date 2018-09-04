<?php

namespace App\Controller;

use App\Entity\TrafficReport;
use App\Form\TrafficReportType;
use App\Repository\TrafficReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->render('traffic_report/index.html.twig', ['traffic_reports' => $trafficReportRepository->findAll()]);
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

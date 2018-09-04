<?php

namespace App\Controller;

use App\Entity\Router;
use App\Form\RouterType;
use App\Repository\RouterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/router")
 */
class RouterController extends Controller
{
    /**
     * @Route("/", name="router_index", methods="GET")
     */
    public function index(RouterRepository $routerRepository): Response
    {
        return $this->render('router/index.html.twig', ['routers' => $routerRepository->findAll()]);
    }

    /**
     * @Route("/admin/new", name="router_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $router = new Router();
        $form = $this->createForm(RouterType::class, $router);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($router);
            $em->flush();

            return $this->redirectToRoute('router_index');
        }

        return $this->render('router/new.html.twig', [
            'router' => $router,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="router_show", methods="GET")
     */
    public function show(Router $router): Response
    {
        return $this->render('router/show.html.twig', ['router' => $router]);
    }

    /**
     * @Route("/admin/{id}/edit", name="router_edit", methods="GET|POST")
     */
    public function edit(Request $request, Router $router): Response
    {
        $form = $this->createForm(Router1Type::class, $router);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('router_edit', ['id' => $router->getId()]);
        }

        return $this->render('router/edit.html.twig', [
            'router' => $router,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/{id}", name="router_delete", methods="DELETE")
     */
    public function delete(Request $request, Router $router): Response
    {
        if ($this->isCsrfTokenValid('delete'.$router->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($router);
            $em->flush();
        }

        return $this->redirectToRoute('router_index');
    }

    /**
     * @Route("/update_traffic_flows", name="traffic_report_live_index", methods="GET")
     */
    public function updateTrafficFlows(RouterRepository $routerRepository) : Response
    {
        //scandire lista router
        //per ogni router
        //prendere la data attuale e formattare l'orario all'intero più piccolo del modulo 5 (minuti)
        //aprire nome file: <cartella><nome_file>
        //<cartella> = router_path/YYYY/MM/DD/
        //<nome_file> = nfcapd.YYYYMMDDhhmm
        //esempio mm = 46 => 5*floor(46/5) = 5*9 = 45
        //eseguire il comando di sistema:
        //  nfdump -M /var/nfsen/profiles-data/live/peer1  -T  -r 2018/09/04/nfcapd.201809040800 -a  -A srcip,dstip -o "fmt:%ts,%td,%sa,%da,%bps" -q
        // fare il parsing dell'output - timestamp - duration - source IP - destinatin IP - bps
        // identificare per quali IP si è sourceIP
        // identificare l'IP del routerOut
        // aggiornare la tabella usando il timestamp iniziale
        // visualizzare risultato con i soli trafficFlows con il timestamp attuale
        //return $this->render('traffic_report/index.html.twig', ['traffic_reports' => $trafficReportRepository->findAll()]);
    }

}

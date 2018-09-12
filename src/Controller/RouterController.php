<?php

namespace App\Controller;

use App\Entity\Router;
use App\Entity\TrafficReport;
use App\Form\RouterType;
use App\Repository\RouterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Datetime;

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
        $form = $this->createForm(RouterType::class, $router);
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
     * @Route("/actions/update_flows", name="router_update_flows", methods="GET|POST")
     * 
     */
    public function updateTrafficFlows(RouterRepository $routerRepository)
    {
        /*$answer = "<h1>Update Traffic Flows</h1>";*/
        //scandire lista router
           //per ogni router
           $extendedTableMultidimentionalArray = array();

           foreach($routerRepository->findAll() as $router){
            //prendere la data attuale e formattare l'orario all'intero più piccolo del modulo 5 (minuti) - anticipato di altri 5 minuti
            $time = new DateTime('now');
            $mday=$time->format('d');
            $month=$time->format('m');
            $year=$time->format('Y');
            $hours=$time->format('H');
            $minutes=$time->format('i');

            $today = $time->getTimestamp(); 
            if($minutes < 5){
                $file_minutes = '55';
                $hours = $hours-1;
                if($hours<10){//ci siamo persi lo zero
                    $hours="0".$hours;
                }
            } elseif($minutes < 10){
                $file_minutes = '00';
            } elseif ($minutes < 15) {
                $file_minutes = '05';
            } else {
                $file_minutes = ($minutes-($minutes%5))-5;
            }

            $rootPath = $router->getFileSystemRootPath();
            $filePath = $year."/".$month."/".$mday."/";
            $fileName = "nfcapd.".$year.$month.$mday.$hours.$file_minutes;
            $command = 'nfdump -M '.$rootPath.' -T -r '.$filePath.$fileName.' -A srcip,dstip -N -o "fmt:%sa,%da,%td,%bps," -q -n 50 -s record/bps \'bps > 10M\'';
            $commandOutput = shell_exec($command);

            $tempTokenArray = array();
            $isSrcIpColumn=true; //il primo token sarà sempre un src IP data la formatttazione dell'output scelta
            $isDstIpColumn=false;
            $isDurationColumn=false;
            $isBwColumn=false;
            $isRouterIn=false;
            $hasTable=false;

            $counter=0;

            $extendedTableHeaders=array(
                'Source Router IP',
                'Source Router Name',
                'Destination Router IP',
                'Destination Router Name',
                'Bandwidht',
                'Source IP',
                'Destination IP'
            );
            $token = strtok($commandOutput, ",");                

            while ($token !== FALSE){
                //check srcIp address
                //echo("<p>Token: ".$token."</p>");
                if($isSrcIpColumn){
                    //echo("<p>isSrcIpColumn</p>");
                    if ($router->amIRouterIN(trim($token,"\x00..\x1F "))){
                        //echo("<p>amIRouterOk</p>");
                        $isRouterIn=true;
                        $tempTokenArray['sourceIP']=trim($token,"\x00..\x1F ");
                        $hasTable=true;
                    } 
                    $isDstIpColumn=true;
                    $isSrcIpColumn=false;
                } elseif ($isDstIpColumn){
                    //$answer .= "<p>isDstIpColumn</p>";
                    if($isRouterIn){
                        $tempTokenArray['routerOut']=$router->getRouterOut(trim($token,"\x00..\x1F "));
                        $tempTokenArray['destinationIP']=trim($token,"\x00..\x1F ");
                    }    
                    $isDstIpColumn=false;
                    $isDurationColumn=true;
                } elseif($isDurationColumn){
                    //$answer .= "<p>isDurationColumn</p>";
                    if($isRouterIn){
                        $tempTokenArray['duration']=$token;
                    }
                    $isDurationColumn=false;
                    $isBwColumn=true;
                } elseif ($isBwColumn){
                    if($isRouterIn){
                        $tempTokenArray['bw']=$token;
                        $trafficReport = new TrafficReport();
                        $trafficReport->setLastTimestamp($time);
                        $trafficReport->setDuration($tempTokenArray['duration']);
                        $trafficReport->setRouterOutIP(ip2long($tempTokenArray['routerOut']));
                        $trafficReport->setBandwidthAsString($tempTokenArray['bw']); // qui problema del formato della bw che non risulta essere un integer
                        $router->addFlowsIN($trafficReport);
                        $extendedTableMultidimentionalArray[]=$router->getIpAddress();
                        $extendedTableMultidimentionalArray[]=$router->getName();
                        $extendedTableMultidimentionalArray[]=$tempTokenArray['routerOut'];
                        $extendedTableMultidimentionalArray[]="To be done";
                        $extendedTableMultidimentionalArray[]=$tempTokenArray['bw'];
                        $extendedTableMultidimentionalArray[]=$tempTokenArray['sourceIP'];
                        $extendedTableMultidimentionalArray[]=$tempTokenArray['destinationIP'];
                        //dump($extendedTableMultidimentionalArray);
                    } 
                    $isRouterIn=false;
                    $isBwColumn=false;
                    $isSrcIpColumn=true; //inizia una nuova riga
                }
                $token = strtok(",");
            } //fine while per parsing elementi restituiti da nfdump
        }//fine loop per router (foreach)
        //dump($extendedTableMultidimentionalArray);
        return $this->render('router/updateTrafficFlows.html.twig', [
            'extendedTableHeaders' => $extendedTableHeaders,
            'extendedTableColumns' => count($extendedTableHeaders),
            'extendedTableMultidimensionalArray' => $extendedTableMultidimentionalArray, 
        ]);

    }
}

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
     * @Route("/actions/update_extended_flows", name="router_update_extended_flows", methods="GET|POST")
     * 
     */
    public function updateExtendedTrafficFlows(RouterRepository $routerRepository)
    {

        $extendedTableMultidimentionalArray = array();
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
        $filePath = $year."/".$month."/".$mday."/";
        $fileName = "nfcapd.".$year.$month.$mday.$hours.$file_minutes;

        foreach($routerRepository->findAll() as $router){
            $rootPath = $router->getFileSystemRootPath();
            $command = 'nfdump -M '.$rootPath.' -T -r '.$filePath.$fileName.' -A srcip,dstip -N -o "fmt:%sa,%da,%td,%bps," -q -n 50 -s record/bps \'bps > 10M\'';
            $commandOutput = shell_exec($command);

            $tempTokenArray = array();
            $isSrcIpColumn=true; //il primo token sarà sempre un src IP data la formatttazione dell'output scelta
            $isDstIpColumn=false;
            $isDurationColumn=false;
            $isBwColumn=false;
            $isRouterIn=false;
            $hasTable=false;

            $extendedTableHeaders=array(
                'Time',
                'Src Router IP',
                'Src Router Name',
                'Dst Router IP',
                'Dst Router Name',
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
                        $extendedTableMultidimentionalArray[]=$mday.'/'.$month.'/'.$year.'-'.$hours.':'.$file_minutes;
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
        return $this->render('router/updateTrafficFlows.html.twig', [
            'TableHeaders' => $extendedTableHeaders,
            'TableColumns' => count($extendedTableHeaders),
            'TableArray' => $extendedTableMultidimentionalArray, 
        ]);
    }

    /**
     * @Route("/actions/update_compact_flows", name="router_update_compact_flows", methods="GET|POST")
     * 
     */
    public function updateCompactTrafficFlows(RouterRepository $routerRepository)
    {
        $TableArray = array();
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
        $filePath = $year."/".$month."/".$mday."/";
        $fileName = "nfcapd.".$year.$month.$mday.$hours.$file_minutes;

        foreach($routerRepository->findAll() as $router){
            $rootPath = $router->getFileSystemRootPath();
            $command = 'nfdump -M '.$rootPath.' -T -r '.$filePath.$fileName.' -A srcip,dstip -N -o "fmt:%sa,%da,%td,%bps," -q -n 50 -s record/bps \'bps > 10M\'';
            $commandOutput = shell_exec($command);
            //echo("<p>Command: ".$command."</p>");

            $isSrcIpColumn=true; //il primo token sarà sempre un src IP data la formatttazione dell'output scelta
            $isDstIpColumn=false;
            $isDurationColumn=false;
            $isBwColumn=false;
            $isRouterIn=false;
            $hasTable=false;

            $tempTokenArray = array();

            $TableHeaders=array(
                'Time',
                'Src Router IP',
                'Src Router Name',
                'Dst Router IP',
                'Dst Router Name',
                'Bandwidht',
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
                        //$tempTokenArray['sourceIP']=trim($token,"\x00..\x1F ");
                        $hasTable=true;
                    } 
                    $isDstIpColumn=true;
                    $isSrcIpColumn=false;
                } elseif ($isDstIpColumn){
                    //$answer .= "<p>isDstIpColumn</p>";
                    if($isRouterIn){
                        $tempTokenArray['routerOutIP']=$router->getRouterOut(trim($token,"\x00..\x1F "));
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
                        $entityManager = $this->getDoctrine()->getManager();
                        $tempTokenArray['bw']=$token;
                        
                        $_outRouter=$this->getDoctrine()->getRepository(Router::class)
                                        ->findOneBy(['ipAddress' => $tempTokenArray['routerOutIP']]);
                                               
                        if(($trafficReport=$router->getExistingFlowIn($time, $tempTokenArray['routerOutIP'])) != NULL){
                            //echo("<p>updateCompactTrafficFlows - found flow updating bw value</p>");
                            $trafficReport->addBpsToBw($tempTokenArray['bw']);
                        } else {
                            //echo("<p>updateCompactTrafficFlows - flow not found - creating new one</p>");
                            $trafficReport = new TrafficReport;
                            $trafficReport->setLastTimestamp($time);
                            $trafficReport->setRouterInName($router->getName());
                            $trafficReport->setRouterInIP($router->getIpAddress());
                            $trafficReport->setRouterOutIP($tempTokenArray['routerOutIP']);
                            $trafficReport->setRouterOut($_outRouter);
                            $trafficReport->setBandwidth($tempTokenArray['bw']);
                            $trafficReport->setRouterIn($router);
                            $router->addFlowsIN($trafficReport);
                            $entityManager->persist($trafficReport);
                        }    
                        $entityManager->persist($router);
                        $entityManager->flush();
                    } 
                    $isRouterIn=false;
                    $isBwColumn=false;
                    $isSrcIpColumn=true; //inizia una nuova riga
                }
                $token = strtok(",");
        
            } //fine while per parsing elementi restituiti da nfdump
            if($hasTable){
                /*echo '<p><pre>TableArray before merge: ';
                print_r($TableArray);
                echo '</pre></p>';*/
                $returnedArray=$router->getTableArray($time, $mday.'/'.$month.'/'.$year.'-'.$hours.':'.$file_minutes);
                /*echo '<p><pre>TableArray returned by router: ';
                print_r($returnedArray);
                echo '</pre></p>'; */
                if(empty($TableArray)){
                    $TableArray = $returnedArray;
                } else {
                    array_merge($TableArray,$returnedArray );
                }
                echo '<p><pre>TableArray after merge: ';
                print_r($TableArray);
                echo '</pre></p>';
            }
            //dump($TableArray);
        
        }//fine loop per router (foreach)

        return $this->render('router/updateTrafficFlows.html.twig', [
            'TableHeaders' => $TableHeaders,
            'TableColumns' => count($TableHeaders),
            'TableArray' => $TableArray, 
        ]);
    }
}

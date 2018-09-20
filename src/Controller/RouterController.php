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
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

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

            return $this->redirectToRoute('router_show', ['id' => $router->getId()]);
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
     * @Route("/admin/deleteallintrafficreports/{id}", name="router_delete_all_in_traffic_reports", methods="DELETE|GET")
     */
    public function deleteAllInTrafficReports(Request $request, Router $router): Response
    {

        foreach($router->getFlowsIn() as $flow){
            $router->removeFlowsIN($flow);
            $flow->getIPRouterHandler()->removeFlowsOut($flow);
            $em = $this->getDoctrine()->getManager();
            $em->remove($flow);
            $em->flush();
        }
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }
    
    /**
     * @Route("/actions/update_extended_flows", name="router_update_extended_flows", methods="GET|POST")
     * 
     */
    public function updateExtendedTrafficFlows(RouterRepository $routerRepository)
    {

        $extendedTableMultidimentionalArray = array();
        $errorsArray = array();
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
            $command = 'nfdump -M '.$rootPath.' -T -r '.$filePath.$fileName.' -A srcip,dstip -N -o "fmt:%sa,%da,%td,%bps," -q -n 0 -s record/bps \'bps > 10M\'';
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
                    $router->getIPRouterHandler();
                    if ($router->amIRouterIN(trim($token,"\x00..\x1F "),$errorsArray)){
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
                        $tempTokenArray['routerOut']=$router->getIPRouterHandler(trim($token,"\x00..\x1F "),$errorsArray);
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
                        $extendedTableMultidimentionalArray[]=$tempTokenArray['bw']/1000000; //Mega bps
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
            'ErrorsArray'=> $errorsArray,
        ]);
    }

    /**
     * @Route("/actions/router_update_flows_loader/{title}/{redirect_to}", name="router_update_flows_loader", methods="GET|POST")
     * 
     */
    public function updateCompactTrafficFlowsLoader($title,$redirect_to)
    {
        //echo("updateCompactTrafficFlowsLoader - Title: ".$title." - Redirect To: ".$redirect_to);
        return $this->render('router/updateTrafficFlowsLoader.html.twig',[
            'title' => $title,
            'redirect_to' => $redirect_to,
        ]);
    }    
    
    /**
     * @Route("/actions/router_update_compact_flows", name="router_update_compact_flows", methods="GET|POST")
     * 
     */    
    public function updateCompactTrafficFlows(RouterRepository $routerRepository)
    {
        $TableArray = array();
        $errorsArray = array();
        $routerList = array();
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

        if(!($routerUnknown = $routerRepository->findOneBy(['ipAddress' => '0.0.0.0']))){
            $errorsArray[] = "Unable to find routerUnknown (0.0.0.0) in repository!!";
            return $this->render('router/updateTrafficFlows.html.twig', [
                'TableHeaders' => $TableHeaders,
                'TableColumns' => count($TableHeaders),
                'TableArray' => $TableArray, 
                'ErrorsArray' => $errorsArray,
            ]);   
        }

        if(!empty($routerList = $routerRepository->findAll())){

            foreach( $routerList as $router){
                $rootPath = $router->getFileSystemRootPath();
                $command = 'nfdump -M '.$rootPath.' -T -r '.$filePath.$fileName.' -A srcip,dstip -N -o "fmt:%sa,%da,%td,%bps," -q -n 50 -s record/bps \'bps > 1M\'';
                $commandOutput = shell_exec($command);
                //echo("<p>Command: ".$command."</p>");
                //echo("<h3>RouterController::updateCompactTrafficFlows - Starting Router Name: ".$router->getName()." - IP: ".$router->getIpAddress()."</h3>");

                $isSrcIpColumn=true; //il primo token sarà sempre un src IP data la formatttazione dell'output scelta
                $isDstIpColumn=false;
                $isDurationColumn=false;
                $isBwColumn=false;
                $isRouterIn=false;
                $hasTable=false;
                $isRouterInUnknown=false;

                $tempTokenArray = array();

                $TableHeaders=array(
                    'Time',
                    'Src Router IP',
                    'Src Router Name',
                    'Dst Router IP',
                    'Dst Router Name',
                    'Bandwidht Mbps',
                );
                $token = strtok($commandOutput, ",");                

                while ($token !== FALSE){
                    //check srcIp address
                    if($isSrcIpColumn){
                        $IPRouterHandler = $this->getIPRouterHandler(trim($token,"\x00..\x1F "),$errorsArray);
                        //echo("<p>RouterController::updateCompactTrafficFlows - IPRouterHandler: ".$IPRouterHandler."</p>");
                        if($IPRouterHandler == "0.0.0.0"){
                            if($routerUnknown){
                                $isRouterInUnknown = true;
                                //echo("<p><b>RouterController::updateCompactTrafficFlows - IsRouterUnknown=true</b></p>");
                            }
                        } elseif($router->getIPAddress() == $IPRouterHandler) {
                            //echo("<p><strong>RouterController::updateCompactTrafficFlows - IsRouterIn(".$router->getName().") = true</strong></p>");
                            $isRouterIn=true;
                            $hasTable=true;
                        } 
                        $isDstIpColumn=true;
                        $isSrcIpColumn=false;
                    } elseif ($isDstIpColumn){
                        //$answer .= "<p>isDstIpColumn</p>";
                        if($isRouterIn || $isRouterInUnknown){
                            $tempTokenArray['routerOutIP']=$router->getIPRouterHandler(trim($token,"\x00..\x1F "),$errorsArray);
                        }    
                        $isDstIpColumn=false;
                        $isDurationColumn=true;
                    } elseif($isDurationColumn){
                        //$answer .= "<p>isDurationColumn</p>";
                        if($isRouterIn || $isRouterInUnknown){
                            $tempTokenArray['duration']=$token;
                        }
                        $isDurationColumn=false;
                        $isBwColumn=true;
                    } elseif ($isBwColumn){
                        if($isRouterIn || $isRouterInUnknown){
                            $tempTokenArray['bw']=$token;

                            $entityManager = $this->getDoctrine()->getManager();
                            if(! ($_outRouter=$this->getDoctrine()->getRepository(Router::class)
                                            ->findOneBy(['ipAddress' => $tempTokenArray['routerOutIP']])))
                            {
                                //echo("<p>RouterController::updateCompactTrafficFlows - Unable to find router out with IP: ".$tempTokenArray['routerOutIP']." in repository!!</p>");
                                $errorsArray[] = "Unable to find router out with IP: ".$tempTokenArray['routerOutIP']." in repository!!";
                                return $this->render('router/updateTrafficFlows.html.twig', [
                                    'TableHeaders' => $TableHeaders,
                                    'TableColumns' => count($TableHeaders),
                                    'TableArray' => $TableArray, 
                                    'ErrorsArray' => $errorsArray,
                                ]);                                    
                            }
                                                                            
                            if($isRouterIn){
                                $trafficReport=$router->getExistingFlowIn($time, $tempTokenArray['routerOutIP']);
                            } else {
                                $trafficReport=$routerUnknown->getExistingFlowIn($time, $tempTokenArray['routerOutIP']);
                            }
                            
                            if($trafficReport != NULL){
                                $trafficReport->addBpsToBw($tempTokenArray['bw']);
                                //echo("<p>RouterController::updateCompactTrafficFlows - Added bw to already existing flow</p>");
                            } else {
                                /*echo("<p>RouterController::updateCompactTrafficFlows - flow not found - creating new one</p>
                                <p>In router: ".$router->getIpAddress()." - Outrouter: ".$tempTokenArray['routerOutIP']."</p>");*/
                                $trafficReport = new TrafficReport;
                                $trafficReport->setLastTimestamp($time);
                                $trafficReport->setRouterOutIP($tempTokenArray['routerOutIP']);
                                $trafficReport->setRouterOut($_outRouter);
                                $trafficReport->setBandwidth($tempTokenArray['bw']);
                                if($isRouterIn){
                                    $trafficReport->setRouterInName($router->getName());
                                    $trafficReport->setRouterInIP($router->getIpAddress());
                                    $trafficReport->setRouterIn($router);
                                    $router->addFlowsIN($trafficReport);
                                } else {
                                    $trafficReport->setRouterInName($routerUnknown->getName());
                                    $trafficReport->setRouterInIP($routerUnknown->getIpAddress());
                                    $trafficReport->setRouterIn($routerUnknown);
                                    $routerUnknown->addFlowsIN($trafficReport);
                                }
                                $entityManager->persist($trafficReport);
                                //echo("<p>RouterController::updateCompactTrafficFlows - Created new trafficReport - Router Out: ".$trafficReport->getRouterOutName()."</p>");
                            }    

                            if($isRouterInUnknown){
                                $entityManager->persist($routerUnknown);
                            } else {
                                $entityManager->persist($router);
                            }

                            $entityManager->flush();
                        } 
                        $isRouterIn=false;
                        $isBwColumn=false;
                        $isRouterInUnknown = false;
                        $isSrcIpColumn=true; //inizia una nuova riga
                    }
                    $token = strtok(",");
            
                } //fine while per parsing elementi restituiti da nfdump
                
                if($hasTable)
                {
                    $returnedArray=$router->getTableArray($time, $mday.'/'.$month.'/'.$year.'-'.$hours.':'.$file_minutes);
                    if(empty($TableArray)){
                        $TableArray = $returnedArray;
                    } else {
                        array_merge($TableArray,$returnedArray );
                    }
                } else {
                    $errorsArray[] = "No incoming flows from router: ".$router->getName();
                }
            }//fine loop per router (foreach)

            $returnedArray=$routerUnknown->getTableArray($time, $mday.'/'.$month.'/'.$year.'-'.$hours.':'.$file_minutes);
        } else {
            $errorsArray[] = "No routers in repository - please create routers before!!"; 
        }// fine controllo esistenza router nel repository
        return $this->render('router/updateTrafficFlows.html.twig', [
            'TableHeaders' => $TableHeaders,
            'TableColumns' => count($TableHeaders),
            'TableArray' => $TableArray, 
            'ErrorsArray' => $errorsArray,
        ]);
    }

    public function getIPRouterHandler(string $flowIP, &$errorsArray){
        //implementare qui chiamata a sf di maurizio per avere IP address del router di terminazione per questo IP
        //echo("<p>RouterController::getIPRouterHandler - Looking for: ".$flowIP." Router handler</p>");
     
        if(filter_var($flowIP,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4)==false){
            //$errorsArray[]="Wrong source IP address format from netflow: ".$flowIP;
            return "0.0.0.101";
        }
     
        $command="python /home/mau/quagga/showipbgp.py -ip ".$flowIP." 2>&1";

        if(($routerHandlerIP = shell_exec($command)) == NULL){
            $errorsArray[]="showibgp command: .".$command." failure!!";
            return "0.0.0.102";
        } elseif (filter_var(trim($routerHandlerIP), FILTER_VALIDATE_IP,FILTER_FLAG_IPV4) == false) {
            //echo("<p>RouterController::getIPRouterHandler - Invalid Router IP format back from showipbgp: ".$routerHandlerIP." Router handler</p>");
            $errorsArray[]="Command: .".$command." - returned not valid Router IP address: ".$routerHandlerIP;
            return "0.0.0.103";
        }
        
        return trim($routerHandlerIP);
    }

}

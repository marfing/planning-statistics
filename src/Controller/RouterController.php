<?php

namespace App\Controller;

use App\Entity\Router;
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
     * @Route("/actions/update_flows", name="router_update_flows", methods="GET|POST")
     * 
     */
    public function updateTrafficFlows(RouterRepository $routerRepository)
    {
        $answer = "<h1>Update Traffic Flows</h1>";
        //scandire lista router
           //per ogni router
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
            } elseif($minutes < 10){
                $file_minutes = '00';
            } elseif ($minutes < 15) {
                $file_minutes = '05';
            } else {
                $file_minutes = ($minutes-($minutes%5))-5;
            }

            //aprire nome file: <cartella><nome_file>
            //<cartella> = router_path/YYYY/MM/DD/
            //<nome_file> = nfcapd.YYYYMMDDhhmm
            //esempio mm = 46 => (46-(46%5))-5 = 46-1 = 45
            $rootPath = $router->getFileSystemRootPath();
            $filePath = $year."/".$month."/".$mday."/";
            $fileName = "nfcapd.".$year.$month.$mday.$hours.$file_minutes;
            $command = 'nfdump -M '.$rootPath.' -T -r '.$filePath.$fileName.' -a -A srcip,dstip -o "fmt:%ts,%td,%sa,%da,%bps,%fl," -q -n 10 -s record/bps \'bps > 100k\'';
            //eseguire il comando di sistema:
            //  nfdump -M /var/nfsen/profiles-data/live/peer1  -T  -r 2018/09/04/nfcapd.201809040800 -a  -A srcip,dstip -o "fmt:%ts,%td,%sa,%da,%bps" -q
            $commandOutput = shell_exec($command);
            // non funziona, resituisce soltanto l'ultima riga dell'output.
            //modificare il command creando un file csv con l'output, parsandolo e poi cancellandolo alla fine
            //dump($commandOutput);
            $token = strtok($commandOutput, ",");                
            //$csvArray = array();
            $answer .= "<p><h2>Router name: ".$router->getName()."</h2></p>
            <p>Month: ".$month."</p>
            <p>Day: ".$mday."</p>
            <p>Year: ".$year."</p>
            <p>Hour: ".$hours."</p>
            <p>Minutes: ".$minutes."</p>
            <p>File minutes: ".$file_minutes."</p>
            <p>Root path: ".$rootPath."</p>
            <p>File path/name: ".$filePath.$fileName."</p>
            <p>Command: ".$command."</p>
            <!-- <p>Command output: ".$commandOutput."</p> -->
            <br>";

            $counter=0;
            $idCounter=0;
            $answer .= "<table>
            <tr>
                <th>Index</th>
                <th>Timestamp</th>
                <th>Duration (msec)</th>
                <th>SourceIP</th>
                <th>DestinationIP</th>
                <th>Bandwidth (bps)</th>
                <th>Flussi</th>
            </tr>
            <tr><td>0</td>";
            $srcIPRoot = 2; //tutti i sourceIP si trovano nella 3° colonna del csv
            $dstIPRoot = 3; //tutti i destinationIP si trovano nella 4° colonna del csv
            $bwRoot = 4; //tutti i valori di banda si trovano nella 4° colonna del csv
            $tempTokenArray = array();
            $isRouterIn=false;
            $getBw=false;
            while ($token !== FALSE){
                //check srcIp address
                if($counter >= $srcIPRoot && !$isRouterIn ){
                    if( ($counter == $srcIPRoot) || ((($counter - $srcIPRoot)%6)==0) ){
                        if ( $router->amIRouterIN($token) ){
                            $isRouterIn=true;
                        }
                    }
                } elseif ($isRouterIn){ //in questo caso so che sto leggendo il destIP
                    $isRouterIn=false;
                    $getBw=true;
                    $tempTokenArray['routerOut']=$router->getRouterOut($token);
                } elseif ($getBw){ //in questo caso so che sto leggendo i valori di bps
                    $getBw=false;
                    // inserire quik la creazione del nuovo trafficReport
                    //abbiamo modificato la chiamata al comando di nfdump per avere già i flussi agggregati per src e dest IP
                    //non serve quindi mai verificare se il flusso già esiste per aggiungergli soltanto i valori di bps
                    $tempTokenArray['bw']=$token;
                }
                $answer .= "<td align=\"center\">".$token."</td>";
                if( ($counter!=0) && (($counter)%6 == 5) ){
                    $idCounter++;
                    $answer .= "</tr><tr><td>".$idCounter."</td>";
                }
                $token = strtok(",");
                $counter++;
            }
            $answer .= "</table>";
            //dump($csvArray);
            // fare il parsing dell'output - timestamp - duration - source IP - destinatin IP - bps
            // identificare per quali IP si è sourceIP
            // identificare l'IP del routerOut
            // aggiornare la tabella usando il timestamp iniziale
        }//fine loop per router
        // visualizzare risultato con i soli trafficFlows con il timestamp attuale */
        //return $this->redirectToRoute('traffic_report_index');
        return new Response("<html><body>".$answer."</body></html>");
    }
}

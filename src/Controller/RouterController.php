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
            if($minutes < 10){
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
            $command = 'nfdump -M '.$rootPath.' -T -r '.$filePath.$fileName.' -a -A srcip,dstip -o "fmt:%ts,%td,%sa,%da,%bps" -q -c 2';
            //eseguire il comando di sistema:
            //  nfdump -M /var/nfsen/profiles-data/live/peer1  -T  -r 2018/09/04/nfcapd.201809040800 -a  -A srcip,dstip -o "fmt:%ts,%td,%sa,%da,%bps" -q
            $commandOutput = system($command);
            // non funziona, resituizsce soltanto l'ultima riga dell'output.
            //modificare il command creando un file csv con l'output, parsandolo e poi cancellandolo alla fine
            dump($commandOutput);
            $answer = $answer . "<p><h2>Router name: ".$router->getName()."</h2></p>
            <p>Month: ".$month."</p>
            <p>Day: ".$mday."</p>
            <p>Year: ".$year."</p>
            <p>Hour: ".$hours."</p>
            <p>Minutes: ".$minutes."</p>
            <p>File minutes: ".$file_minutes."</p>
            <p>Root path: ".$rootPath."</p>
            <p>File path/name: ".$filePath.$fileName."</p>
            <p>Command: ".$command."</p>
            <p>Command output: ".$commandOutput."</p>
            <br>
            <table>
                <tr>
                    <th>Timestamp</th>
                    <th>Duration</th>
                    <th>SourceIP</th>
                    <th>DestinationIP</th>
                    <th>Bandwidth</th>
                </tr>";
                
            $token = strtok($commandOutput, ",");                
            $csvArray = array();
            while ($token !== FALSE){
                $csvArray[] = $token;
                $token = strtok(",");
            }
            dump($csvArray);
            $num = count($csvArray);
            if($num > 4){
                $answer=$answer."<tr>";
                $innerCounter=0;
                for($counter=0; $counter < $num; $counter++){
                    $answer=$answer."<td>".$csvArray[$counter]."</td>";
                    $innerCounter++;
                    if($innerCounter==5){
                        $innerCounter=0;
                        $answer=$answer."</tr>";
                        if($counter < $num){
                            $answer=$answer."<tr>";
                        }
                    }
                }// fine sequenza array valori csv
            } 
            $answer=$answer."</table>";

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

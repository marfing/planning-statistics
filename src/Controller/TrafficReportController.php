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
            ->add('graph', SubmitType::class, array('label' => 'Graph'))
            ->getForm();

        return $this->render('traffic_report/index.html.twig', array(
            'traffic_reports' => $trafficReportRepository->findAll(),
            'compact' => false,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/selected/{routerInIP}", name="traffic_report_selected_index", methods="GET")
     */
    public function selectedIndex(TrafficReportRepository $trafficReportRepository, string $routerInIP): Response
    {        
        return $this->render('traffic_report/index.html.twig', [
            'traffic_reports' => $trafficReportRepository->findBy(['routerInIP' => $routerInIP]),
            'compact' => false,            
        ]);
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
            ->add('graph', SubmitType::class, array('label' => 'Graph'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $dateInterval = $form->getData();
            $startTime = $dateInterval->getStartDate();
            $endTime = $dateInterval->getEndDate();

            if($form->get('graph')->isClicked()){
                return $this->graph($request, $trafficReportRepository, $startTime, $endTime);
            }

            $aggregate = $dateInterval->getAggregate();
            $fullTrafficReportArray = $trafficReportRepository->findByTimeInterval($startTime, $endTime); 


            $aggregatedTrafficReportArray = array();

            if(!$aggregate)
            {
                return $this->render('traffic_report/index.html.twig', [
                'traffic_reports' => $fullTrafficReportArray,
                'compact' => false,
                'form' => $form->createView(),
                ]);
            } else { //l'ozione aggrega è stata selezionata nella check box del form
                foreach($fullTrafficReportArray as $originalTrafficReport){
                    $compactReportExist = false;
                    foreach($aggregatedTrafficReportArray as $aggregatedTrafficReport){
                        if( ($originalTrafficReport->getRouterInIP() == $aggregatedTrafficReport->getRouterInIP()) && 
                            ($originalTrafficReport->getRouterOutIP() == $aggregatedTrafficReport->getRouterOutIP())){
                            $aggregatedTrafficReport->addSample($originalTrafficReport->getBandwidth());
                            $compactReportExist = true;
                        }
                    }
                    if(!$compactReportExist){
                        $newReport = new TrafficReport();
                         $newReport->setRouterInIP($originalTrafficReport->getRouterInIP());
                        $newReport->setRouterOutIP($originalTrafficReport->getRouterOutIP());
                        $newReport->setRouterInName($originalTrafficReport->getRouterInName());
                        $newReport->setRouterOutName($originalTrafficReport->getRouterOutName());
                        $newReport->setMegaBandwidth($originalTrafficReport->getBandwidth());
                        $aggregatedTrafficReportArray[] = $newReport;
                    }
                }
                return $this->render('traffic_report/index.html.twig', [
                    'traffic_reports' => $aggregatedTrafficReportArray,
                    'compact' => true,
                    'form' => $form->createView(),
                    ]);
            }
        }
        return $this->render('traffic_report/index.html.twig', array(
            'traffic_reports' => $trafficReportRepository->findAll(),
            'compact' => false,
            'form' => $form->createView(),
        ));
    }


    /**
     * @Route("/graph", name="traffic_reports_graph", methods="GET")
     */
    public function graph(Request $request, TrafficReportRepository $trafficReportRepository, \DateTime $start, \DateTime $end ): Response
    {
        //da repository accedo alla lista dei router e definisco gli indici per le colonne del grafico
        //scandisco la lista dei traffic report, per ogni nuova data creo array[data]=[0,0,0,...,0]
        //identifico l'indice del primo router con quella data e aggiorno il valore della sua colonna per quella data
        //se la data esiste già, cerco l'indice di quel router e aggiorno la sua colonna sommandovi il nuovo valore di bw
        //alla fine ottengo un array multidimensionale indicizzato con le date, da cui generare l'array di dati per la chart di google

        $fullTrafficReportArray = $trafficReportRepository->findByTimeInterval($start, $end); 
        $aggregatedTrafficReportArray = array();
        $listOfRoutersIn = array(); //lista ordinata dei nomi dei router da graficare
        $listOfTimestamps = array();
        $graphValues = array();
        
        foreach($fullTrafficReportArray as $originalTrafficReport){
            $compactReportExist = false;
            if(!in_array($originalTrafficReport->getRouterInName(),$listOfRoutersIn)){
                $listOfRoutersIn[]=$originalTrafficReport->getRouterInName();
            }
            if(!in_array($originalTrafficReport->getLastTimestamp(),$listOfTimestamps)){
                $listOfTimestamps[]=$originalTrafficReport->getLastTimestamp();
            }
            foreach($aggregatedTrafficReportArray as $aggregatedTrafficReport){ //somma i valori di banda su base RouterIn e timestamp
                if( ($originalTrafficReport->getRouterInName() == $aggregatedTrafficReport->getRouterInName()) && 
                    ($originalTrafficReport->getLastTimestamp() == $aggregatedTrafficReport->getLastTimestamp()))
                {
                    $aggregatedTrafficReport->addSample($originalTrafficReport->getBandwidth());
                    $compactReportExist = true;
                }
            }
            //se RouterIn&timestamp non è in lista, lo aggiungiamo
            if(!$compactReportExist){
                $newReport = new TrafficReport();
                $newReport->setRouterInIP('1.1.1.1');
                $newReport->setRouterOutIP('1.1.1.1');
                $newReport->setRouterInName($originalTrafficReport->getRouterInName());
                $newReport->setRouterOutName('useless');
                $newReport->setMegaBandwidth($originalTrafficReport->getBandwidth());
                $newReport->setLastTimestamp($originalTrafficReport->getLastTimestamp());
                $aggregatedTrafficReportArray[] = $newReport;
            }
        }
        //passare lista dei router che almeno una volta sono stati routerin
        //passare matrice [datetime,bwR1,bwR2.....,bwRn] seguendo gli stessi indici dell'array dei router che sono stati almeno una volta routerin
        $numberOfRouters = count($listOfRoutersIn);
        $rowCounter = 0;
        foreach($listOfTimestamps as $timestamp){
            //inizializzo la riga da passare al grafico con timestamp  e tutti i valori di banda a zero
            $graphValues[$rowCounter][0]=$timestamp;
            for ($i=1; $i<=$numberOfRouters; $i++){
                $graphValues[$rowCounter][$i]=0;
            }
            //modifico i soli valori di banda dei router che hanno fatto traffico in quel timestamp
            foreach ($aggregatedTrafficReportArray as $report){
                if($report->getLastTimestamp()==$timestamp){
                    $index=array_search($report->getRouterInName(),$listOfRoutersIn);
                    if(!($index === false)){
                        $graphValues[$rowCounter][$index+1]=$report->getBandWidth();
                    }
                }
            }
            $rowCounter++;
        }
        dump($graphValues);
        return $this->render('traffic_report/graph.html.twig', [
            'routers' => $listOfRoutersIn,
            'graphValues' => $graphValues,
            'rowsNumber' => count($listOfTimestamps),
            'columnsNumber' => count($listOfRoutersIn)
            ]);

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

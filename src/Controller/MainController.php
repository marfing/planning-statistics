<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller{

    /**
     * @Route("/", name="home", methods="GET")
     */
    public function index()
    {
        #marfi - implementare qui il reload dei file csv
        
        return $this->render('/home/home.html.twig');
    }

    /**
     * @Route("/erlangb", name="erlangb", methods="GET")
     */
    public function erlangB()
    {
        #marfi - implementare qui formule per calcolo capacità traffico
        
        return $this->render('/erlangb/erlangb.html.twig');
    }

    /**
     * @Route("/erlangb/minutes", name="erlangb_minutes", methods="GET")
     */
    public function erlangBminutes()
    {
        #marfi - implementare qui formule per calcolo capacità traffico
        
        return $this->render('/erlangb/erlangb_minutes.html.twig');
    }
}


<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller{

    /**
     * @Route("/home", name="home", methods="GET")
     */
    public function index()
    {
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


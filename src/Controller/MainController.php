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
        return $this->render('/home/home.html.twig');
    }
}


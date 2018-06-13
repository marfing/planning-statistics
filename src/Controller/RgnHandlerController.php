<?php

namespace App\Controller;

use App\Entity\RgnHandler;
use App\Form\RgnHandlerType;
use App\Repository\RgnHandlerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RgnHandlerController extends Controller
{
    /**
     * @Route("/rgn/cli_map", name="cli_rgn_map")
     */
    public function newCliListMapping(Request $request): Response
    {
        $rgnHandler = new RgnHandler();
        $form = $this->createForm(RgnHandlerType::class, $rgnHandler);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if($request->request->get('map') && $form->isValid()){
                $rgnHandler->newCliRgnMap();
//                return new Response("<h1>Fine ricerca</h1>");
                return $this->render('rgnHandler/cli_map_result.html.twig', [
                    'rgnHandler' => $rgnHandler]);
            } //l'alternativa può solo essere il cancel
            return $this->redirectToRoute('home');
        }

        return $this->render('rgnHandler/cli_map.html.twig', [
            'rgnHandler' => $rgnHandler,
            'form' => $form->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\FeasibilityB2B;
use App\Form\FeasibilityB2BType;
use App\Repository\FeasibilityB2BRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 * @Route("/feasibility/b2/b")
 */
class FeasibilityB2BController extends Controller
{
    /**
     * @Route("/", name="feasibility_b2b_index", methods="GET")
     */
    public function index(FeasibilityB2BRepository $feasibilityB2BRepository, Security $security, AuthorizationCheckerInterface $authChecker): Response
    {        
        if($authChecker->isGranted('ROLE_ADMIN')){
            return $this->render('feasibility_b2b/index.html.twig', ['feasibility_b2_bs' => $feasibilityB2BRepository->findAll()]);
        } 
        return $this->render('feasibility_b2b/index.html.twig', ['feasibility_b2_bs' => 
                                                            $feasibilityB2BRepository->findByUserId($security->getUser()->getId())]);
}

    /**
     * @Route("/new", name="feasibility_b2b_new", methods="GET|POST")
     */
    public function new(Request $request, \Swift_Mailer $mailer): Response
    {
        $feasibilityB2B = new FeasibilityB2B();
        $user=$this->getUser();
        $form = $this->createForm(FeasibilityB2BType::class, $feasibilityB2B);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $feasibilityB2B->setUser($user);
            $feasibilityB2B->setStatus("new");
            $em->persist($feasibilityB2B);
            $em->flush();

            $message = (new \Swift_Message('New B2B Feasibility') )
                ->setFrom('planning.rete@gmail.com')
                ->setTo('mfigus@it.tiscali.com')
                ->setBody(
                    $this->renderView(
                        'feasibility_b2b/mail.html.twig', 
                            array('feasibility_b2_b' => $feasibilityB2B)
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);
            $message2 = (new \Swift_Message('New B2B Feasibility') )
                ->setFrom('planning.rete@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'feasibility_b2b/mail.html.twig', 
                            array('feasibility_b2_b' => $feasibilityB2B)
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message2);

            return $this->redirectToRoute('feasibility_b2b_index');
        }

        return $this->render('feasibility_b2b/new.html.twig', [
            'feasibility_b2_b' => $feasibilityB2B,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="feasibility_b2b_show", methods="GET")
     */
    public function show(FeasibilityB2B $feasibilityB2B): Response
    {
        return $this->render('feasibility_b2b/show.html.twig', ['feasibility_b2_b' => $feasibilityB2B]);
    }

    /**
     * @Route("/{id}/edit", name="feasibility_b2b_edit", methods="GET|POST")
     */
    public function edit(Request $request, FeasibilityB2B $feasibilityB2B, \Swift_Mailer $mailer): Response
    {
        $user=$this->getUser();
        $form = $this->createForm(FeasibilityB2BType::class, $feasibilityB2B, ['role' => $user->getRoles()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $message = (new \Swift_Message('New B2B Feasibility') )
                ->setFrom('planning.rete@gmail.com')
                ->setTo('mfigus@it.tiscali.com')
                ->setBody(
                    $this->renderView(
                        'feasibility_b2b/mail.html.twig', 
                            array('feasibility_b2_b' => $feasibilityB2B)
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);
            $message2 = (new \Swift_Message('New B2B Feasibility') )
                ->setFrom('planning.rete@gmail.com')
                ->setTo($feasibilityB2B->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        'feasibility_b2b/mail.html.twig', 
                            array('feasibility_b2_b' => $feasibilityB2B)
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message2);
            return $this->redirectToRoute('feasibility_b2b_show', ['id' => $feasibilityB2B->getId()]);
        }

        return $this->render('feasibility_b2b/edit.html.twig', [
            'feasibility_b2_b' => $feasibilityB2B,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="feasibility_b2b_delete", methods="DELETE")
     */
    public function delete(Request $request, FeasibilityB2B $feasibilityB2B): Response
    {
        if ($this->isCsrfTokenValid('delete'.$feasibilityB2B->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $userOwner = $feasibilityB2B->getUser();
            $userOwner->removeFeasibilitiesB2B($feasibilityB2B);
            $em->remove($feasibilityB2B);
            $em->flush();
        }

        return $this->redirectToRoute('feasibility_b2b_index');
    }
}

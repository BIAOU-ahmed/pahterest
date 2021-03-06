<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\createFormBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\redirectToRoute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{

    /**
     * @Route("/", name="pins_home", methods={"GET"})
     */
    public function index(PinRepository $pinRepository): Response
    {
    	$pins = $pinRepository->findBy([],['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', compact('pins'));
    }


    /**
     * @Route("/pin/{id<[0-9]+>}", name="pins_show", methods={"GET"})
     */
    public function show(Pin $pin):Response
    {
        
        return $this->render('pins/show.html.twig',compact('pin'));
    }


    /**
     * @Route("/pin/create", name="pins_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
        $pin = new Pin;

       $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $janeDoe = $userRepo->findOneBy(['email' => 'janedoe@example.com']);
            $pin->setUser($janeDoe);
            
            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'Pin successfully created!');
            return $this->redirectToRoute('pins_home');

        }

        return $this->render('pins/create.html.twig',[
            'form'  => $form->createview()
        ]); 
    }




    /**
     * @Route("/pin/{id<[0-9]+>}/edit", name="pins_edit", methods={"GET", "PUT"})
     */
    public function edit(Pin $pin, Request $request, EntityManagerInterface $em):Response
    {
        
        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'

        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

           
            $em->flush();

            $this->addFlash('success', 'Pin successfully edited!');
            return $this->redirectToRoute('pins_home');

        }

        return $this->render('pins/edit.html.twig',[
            'pin' => $pin,
            'form'  => $form->createview()
        ]); 
    }



   /**
     * @Route("/pin/{id<[0-9]+>}", name="pins_delete", methods={"DELETE",})
     */
    public function delete(Request $request, Pin $pin, EntityManagerInterface $em):Response
    {
        if ($this->isCsrfTokenValid('pins_deletion_' . $pin->getId(), $request->request->get('csrf_token'))) {

            $em->remove($pin);
            $em->flush();


            $this->addFlash('info', 'Pin successfully deleted!');
           
        }

        return $this->redirectToRoute('pins_home');
    }
}

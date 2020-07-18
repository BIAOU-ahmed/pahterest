<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
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
     * @Route("/pin/{id<[0-9]+>}/edit", name="pins_edit", methods={"GET", "POST"})
     */
    public function edit(Pin $pin, Request $request, EntityManagerInterface $em):Response
    {
        
        $form = $this->createFormBuilder($pin)
            ->add('title', null, [
                'attr' => ['autofocus' => true]])
            ->add('description', null,['attr' =>['clos'=>50, 'rows' => 10]])
            ->getForm()
        ;

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

           
            $em->flush();

            return $this->redirectToRoute('pins_home');

        }

        return $this->render('pins/edit.html.twig',[
            'pin' => $pin,
            'form'  => $form->createview()
        ]); 
    }



    /**
     * @Route("/pin/create", name="pins_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;
        $form = $this->createFormBuilder($pin)
            ->add('title', null, [
                'attr' => ['autofocus' => true]])
            ->add('description', null,['attr' =>['clos'=>50, 'rows' => 10]])
            ->getForm()
        ;

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

           
            $em->persist($pin);
            $em->flush();

            return $this->redirectToRoute('pins_show', ['id' => $pin->getId()]);

        }

        return $this->render('pins/create.html.twig',[
            'form'  => $form->createview()
        ]); 
    }

}

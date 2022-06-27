<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntrepriseController extends AbstractController
{
    /**
     * @Route("/entreprise", name="app_entreprise")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $entreprises = $doctrine->getRepository(Entreprise::class)->findBy([], ['raisonSociale' => 'ASC']);
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises,
        ]);
    }

    /**
     * @Route("/entreprise/add", name="add_entreprise")
     * @Route("/entreprise/update/{id}", name="update_entreprise")
     */
    public function add(ManagerRegistry $doctrine, Entreprise $entreprise = null, Request $request): Response
    {
        if(!$entreprise) {
            $entreprise = new Entreprise();
        }

        $entityManager = $doctrine->getManager();
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entreprise = $form->getData(); 
            $entityManager->persist($entreprise);
            $entityManager->flush();

            return $this->redirectToRoute('app_entreprise');
        }

        return $this->renderForm('entreprise/add.html.twig', [
            'form' => $form,
            'editMode' => $entreprise->getId()
        ]);
    }

    /**
     * @Route("/entreprise/delete/{id}", name="delete_entreprise")
     */
    public function delete(ManagerRegistry $doctrine, Entreprise $entreprise) {
        
        $entityManager = $doctrine->getManager();
        $entityManager->remove($entreprise);
        $entityManager->flush(); 
        return $this->redirectToRoute("app_entreprise");
    }

    /**
     * @Route("/entreprise/{id}", name="show_entreprise")
     */
    public function show(Entreprise $entreprise): Response
    {
        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }

}

<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class EmployeController extends AbstractController
{
    /**
     * @Route("/employe", name="app_employe")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $employes = $doctrine->getRepository(Employe::class)->findBy([], ['nom' => 'ASC']);
        return $this->render('employe/index.html.twig', [
            'employes' => $employes,
        ]);
    }

    /**
     * @Route("/employe/add", name="add_employe")
     * @Route("/employe/update/{id}", name="update_employe")
     */
    public function add(ManagerRegistry $doctrine, Employe $employe = null, SluggerInterface $slugger, Request $request): Response
    {
        if(!$employe) {
            $employe = new Employe();
        }

        $entityManager = $doctrine->getManager();
        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $employe = $form->getData();
            $currentPhoto = $employe->getPhoto();
            $photo = $form->get('photo')->getData();

            if ($photo instanceof UploadedFile) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('employes_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->redirectToRoute('app_employe');
                }

                $employe->setPhoto($newFilename);
            } else {
                if($currentPhoto == null) {
                    $employe->setPhoto('notavailable.jpeg');
                } else {
                    $employe->setPhoto($currentPhoto);
                }
            }

            $entityManager->persist($employe);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_employe');
        }

        return $this->renderForm('employe/add.html.twig', [
            'form' => $form,
            'editMode' => $employe->getId()
        ]);
    }

    /**
     * @Route("/employe/delete/{id}", name="delete_employe")
     */
    public function delete(ManagerRegistry $doctrine, Employe $employe) {
        
        $entityManager = $doctrine->getManager();
        unlink($this->getParameter('employes_directory').$employe->getPhoto());
        $entityManager->remove($employe);
        $entityManager->flush(); 
        return $this->redirectToRoute("app_employe");
    }

    /**
     * @Route("/employe/{id}", name="show_employe")
     */
    public function show(Employe $employe = null): Response
    {
        if($employe == null) {
            return $this->redirectToRoute("app_employe");
        } else {
            return $this->render('employe/show.html.twig', [
                'employe' => $employe,
            ]);
        }
    }
}

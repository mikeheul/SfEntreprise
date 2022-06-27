<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\SearchType;
use App\Repository\EmployeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="app_search")
     */
    public function index(): Response
    {
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }

    public function search(Employe $employe = null) {

        // $form = $this->createForm(SearchType::class, $employe);
        
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('handleSearch'))
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez un mot-clé'
                ]
            ])
            // ->add('rechercher', SubmitType::class, [
            //     'attr' => [
            //         'class' => 'btn btn-secondary'
            //     ],
            //     'label' => 'Rechercher'
            // ])
            ->getForm();
        
        return $this->render('search/searchBar.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/handleSearch", name="handleSearch")
     * @param Request $request
     */
    public function handleSearch(Request $request, EmployeRepository $repo)
    {
        $query = $request->request->get('form')['query'];
        if($query) {
            $employes = $repo->findEmployesByName($query);
        }
        return $this->render('search/index.html.twig', [
            'employes' => $employes
        ]);
    }
}

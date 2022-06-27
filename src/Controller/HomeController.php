<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Entity\Entreprise;
use Symfony\UX\Chartjs\Model\Chart;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ManagerRegistry $doctrine, ChartBuilderInterface $chartBuilder): Response
    {
        $entityManager = $doctrine->getManager();
        $nbEntreprises = count($entityManager->getRepository(Entreprise::class)->findAll());
        $nbEmployes = count($entityManager->getRepository(Employe::class)->findAll());

        // $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        // $chart->setData([
        //     'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        //     'datasets' => [
        //         [
        //             'label' => 'My First dataset',
        //             'backgroundColor' => 'rgb(255, 99, 132)',
        //             'borderColor' => 'rgb(255, 99, 132)',
        //             'data' => [0, 10, 5, 2, 20, 30, 45],
        //         ],
        //     ],
        // ]);

        // $chart->setOptions([
        //     'scales' => [
        //         'y' => [
        //             'suggestedMin' => 0,
        //             'suggestedMax' => 100,
        //         ],
        //     ],
        // ]);

        return $this->render('home/index.html.twig', [
            'nbEntreprises' => $nbEntreprises,
            'nbEmployes' => $nbEmployes,
            // 'chart' => $chart
        ]);
    }
}
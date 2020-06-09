<?php

namespace App\Controller\App;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 *
 * @see \App\Tests\Controller\App\DashboardControllerTest
 */
final class DashboardController extends AbstractController
{
    /**
     * @Route("/app/dashboard", name="app_dashboard")
     */
    public function index(): Response
    {
        $this->getDoctrine();

        return $this->render('app/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}

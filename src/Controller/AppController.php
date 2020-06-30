<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app", name="app_")
 * @IsGranted("ROLE_USER")
 *
 * @see \App\Tests\Controller\AppControllerTest
 */
final class AppController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index(): Response
    {
        return $this->render('app/index.html.twig');
    }
}

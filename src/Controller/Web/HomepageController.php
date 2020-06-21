<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @see \App\Tests\Controller\Web\HomepageControllerTest
 */
final class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        return $this->render('Web/homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}

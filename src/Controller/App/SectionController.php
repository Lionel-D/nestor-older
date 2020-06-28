<?php

namespace App\Controller\App;

use App\Service\App\SectionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SectionController extends AbstractController
{
    /**
     * @Route("/app/section", name="app_section")
     */
    public function index(SectionService $sectionService)
    {
        $sectionList = $sectionService->getList();

        return $this->render('app/section/index.html.twig', [
            'section_list' => $sectionList,
        ]);
    }
}

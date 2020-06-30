<?php

namespace App\Service\App;

use App\Entity\App\Section;
use App\Repository\App\SectionRepository;

class SectionService
{
    /**
     * @var SectionRepository
     */
    private $sectionRepository;

    public function __construct(SectionRepository $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }

    /**
     * @return Section[]
     */
    public function getList()
    {
        return $this->sectionRepository->findAll();
    }
}

<?php

namespace App\Service\App;

use App\Entity\Section;
use App\Repository\SectionRepository;

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

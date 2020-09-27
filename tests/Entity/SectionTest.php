<?php

namespace App\Tests\Entity;

use App\Entity\Section;
use PHPUnit\Framework\TestCase;

final class SectionTest extends TestCase
{
    /**
     * @var Section
     */
    private $section;

    protected function setUp(): void
    {
        parent::setUp();

        $this->section = new Section();
    }

    public function testName(): void
    {
        $this->section->setName('Légumes frais');

        $this->assertTrue('Légumes frais' === $this->section->getName());
    }

    public function testDescription(): void
    {
        $this->section->setDescription('Salades, Tomates, Oignons...');

        $this->assertTrue('Salades, Tomates, Oignons...' === $this->section->getDescription());
    }

    public function testImageFilename(): void
    {
        $this->section->setImageFilename('legumes.jpg');

        $this->assertTrue('legumes.jpg' === $this->section->getImageFilename());
    }
}

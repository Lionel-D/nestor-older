<?php

namespace App\DataFixtures;

use App\Entity\Section;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SectionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $freshVegetables = new Section();

        $freshVegetables->setName("Légumes frais");
        $freshVegetables->setDescription("Salades, Tomates, Oignons...");

        $manager->persist($freshVegetables);

        $freshFruits = new Section();

        $freshFruits->setName("Fruits frais");
        $freshFruits->setDescription("Pommes, Poires, Scoubidouah...");

        $manager->persist($freshFruits);

        $bakery = new Section();

        $bakery->setName("Boulangerie");
        $bakery->setDescription("Pains, Viennoiseries, Gâteaux...");

        $manager->persist($bakery);

        $manager->flush();
    }
}

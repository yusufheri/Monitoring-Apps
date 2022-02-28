<?php

namespace App\DataFixtures;


use App\Entity\Website;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr-FR');



        $website = new Website();
        $website->setCreatedAt(new \DateTimeImmutable())
            ->setName('OptSolution')
            ->setUrl('https://optsolution.net')
            ->setDescription($faker->paragraph(1));;
        $manager->persist($website);

        $website = new Website();
        $website->setCreatedAt(new \DateTimeImmutable())
            ->setName('Demo')
            ->setUrl('http://demo.optsolution.net')
            ->setDescription($faker->paragraph(1));;
        $manager->persist($website);

        for ($i = 0; $i < 19; $i++) {
            $domaine = $faker->domainName;

            $website = new Website();
            $website
                ->setCreatedAt(new \DateTimeImmutable())
                ->setName(explode(".", $domaine)[0])
                ->setUrl($domaine)
                ->setDescription($faker->paragraph(1));
            $manager->persist($website);
        }


        $manager->flush();
    }
}

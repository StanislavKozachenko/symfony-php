<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ServiceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $service = new Service();
        $service->setName('Warranty');
        $service->setCost(50);
        $service->setReleaseDate(new \DateTime());
        $manager->persist($service);

        $service_2 = new Service();
        $service_2->setName('Delivery');
        $service_2->setCost(25);
        $service_2->setReleaseDate(new \DateTime());
        $manager->persist($service_2);

        $service_3 = new Service();
        $service_3->setName('Installation');
        $service_3->setCost(15);
        $service_3->setReleaseDate(new \DateTime());
        $manager->persist($service_3);

        $service_4 = new Service();
        $service_4->setName('Configuration');
        $service_4->setCost(25);
        $service_4->setReleaseDate(new \DateTime());
        $manager->persist($service_4);

        $manager->flush();

        $this->addReference('service_1', $service);
        $this->addReference('service_2', $service_2);
        $this->addReference('service_3', $service_3);
        $this->addReference('service_4', $service_4);
    }
}

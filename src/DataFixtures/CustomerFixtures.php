<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $customer = new Customer();
        $customer->setName('Intel');
        $manager->persist($customer);

        $customer_2 = new Customer();
        $customer_2->setName('A4Tech');
        $manager->persist($customer_2);

        $customer_3 = new Customer();
        $customer_3->setName('Xiaomi');
        $manager->persist($customer_3);

        $manager->flush();

        $this->addReference('customer_1', $customer);
        $this->addReference('customer_2', $customer_2);
        $this->addReference('customer_3', $customer_3);
    }
}

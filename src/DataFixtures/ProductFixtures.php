<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setName('Computer');
        $product->setDescription('A device which is designed or permits the end user to install
        software applications.');
        $product->setCustomer($this->getReference('customer_1'));
        $product->setCost(200);
        $product->setReleaseDate(new \DateTime());
        $product->addService($this->getReference('service_1'));
        $product->addService($this->getReference('service_2'));
        $product->addService($this->getReference('service_3'));
        $product->addService($this->getReference('service_4'));
        $manager->persist($product);

        $product_2 = new Product();
        $product_2->setName('Keyboard');
        $product_2->setDescription('A keyboard is for putting information including letters, 
        words and numbers into your computer!');
        $product_2->setCost(23);
        $product_2->setCustomer($this->getReference('customer_2'));
        $product_2->setReleaseDate(new \DateTime());
        $product_2->addService($this->getReference('service_1'));
        $product_2->addService($this->getReference('service_2'));
        $product_2->addService($this->getReference('service_3'));
        $manager->persist($product_2);

        $product_3 = new Product();
        $product_3->setName('Mouse');
        $product_3->setDescription('Is a hand-held pointing device that detects two-dimensional 
        motion relative to a surface.');
        $product_3->setCost(59);
        $product_3->setCustomer($this->getReference('customer_3'));
        $product_3->setReleaseDate(new \DateTime());
        $product_3->addService($this->getReference('service_2'));
        $product_3->addService($this->getReference('service_3'));
        $product_3->addService($this->getReference('service_4'));
        $manager->persist($product_3);

        $manager->flush();
    }
}

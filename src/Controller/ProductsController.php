<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\Service;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    #[Route('/products', name: 'products')]
    public function index(EntityManagerInterface $em): Response
    {
        $repository = $em->getRepository(Product::class);
        $products = $repository->findAll();

        return $this->render('index.html.twig', array(
            "products"=>$products
        ));
    }

    #[Route('/products/{id}', name: 'product')]
    public function show(EntityManagerInterface $em, $id): Response
    {
        $repository = $em->getRepository(Product::class);
        $product = $repository->find($id);

        return $this->render('show.html.twig', array(
            "product"=>$product,
            "id"=>$id
        ));
    }

    #[Route('/menu/product/add', name: 'product/add')]
    public function addProduct(EntityManagerInterface $em, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $customers = $em->getRepository(Customer::class)->findAll();
        $services = $em->getRepository(Service::class)->findAll();

        if($request->request->get('name') && $request->request->get('cost') && $request->request->get('customer') && $request->request->get('description')) {
            $product = new Product();
            $product->setName($request->request->get('name'));
            $product->setCost($request->request->get('cost'));
            $product->setDescription($request->request->get('description'));
            $product->setReleaseDate(new \DateTime());
            $product->setCustomer($em->getRepository(Customer::class)->find($request->request->get('customer')));

            if($request->request->get('checkboxes')) {
                $repository = $em->getRepository(Service::class);
                $i = 0;
                while ($i < count($request->request->get('checkboxes'))){
                    $service = $repository->find($request->request->get('checkboxes')[$i]);
                    $product->addService($service);
                    $i++;
                }
            }

            $em->persist($product);
            $em->flush();
            return $this->render('products/add.html.twig', array("status"=>true, "customers"=>$customers, "services"=>$services));
        }
        else {
            return $this->render('products/add.html.twig', array("status"=>false, "customers"=>$customers, "services"=>$services));
        }

    }

    #[Route('/menu/product/edit', name: 'product/edit')]
    public function editProduct(EntityManagerInterface $em, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $customers = $em->getRepository(Customer::class)->findAll();
        $services = $em->getRepository(Service::class)->findAll();
        $products = $em->getRepository(Product::class)->findAll();

        if($request->request->get('product-name') && $request->request->get('product-cost') && $request->request->get('product-customer') && $request->request->get('product-description')) {
            $i = 0;
            foreach ($products as $product){
                $product->setName($request->request->get('product-name')[$i]);
                $product->setCost($request->request->get('product-cost')[$i]);
                $product->setDescription($request->request->get('product-description')[$i]);
                $time = DateTime::createFromFormat('d/m/Y',$request->request->get('product-release')[$i]);
                $product->setReleaseDate($time);
                $product->setCustomer($em->getRepository(Customer::class)->find($request->request->get('product-customer')[$i]));
                $product->clearServices();
                if($request->request->get('product-checkboxes')) {
                    $keys = array_keys($_POST['product-checkboxes']);
                        foreach ($keys as $key) {
                            if ($product->getId() == $key) {
                                foreach ($_POST['product-checkboxes']["$key"] as $serviceId) {
                                    $service = $em->getRepository(Service::class)->find($serviceId);
                                    $product->addService($service);
                                }
                            }
                        }
                }
                $i++;
            }
            $em->flush();
            return $this->render('products/edit.html.twig', array("status"=>true, "customers"=>$customers, "services"=>$services, "products"=>$products));
        }
        else {
            return $this->render('products/edit.html.twig', array("status"=>false, "customers"=>$customers, "services"=>$services, "products"=>$products));
        }
    }
    #[Route('/menu/product/delete', name: 'product/delete')]
    public function deleteProduct(EntityManagerInterface $em): \Symfony\Component\HttpFoundation\Response
    {
        $repository = $em->getRepository(Product::class);
        $products = $repository->findAll();
        if(isset($_POST['checkboxes'])) {
            $i = 0;
            while ($i < count($_POST['checkboxes'])){
                $product = $repository->find($_POST['checkboxes'][$i]);
                $em->remove($product);
                $i++;
            }
            $em->flush();
        }
        return $this->render('products/delete.html.twig', array(
            "products"=>$products
        ));
    }
}

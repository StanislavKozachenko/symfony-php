<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\ExchangeRate;
use App\Entity\Product;
use App\Entity\Service;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;
use Helpers\AwsServices;
use Helpers\Filter;
use Helpers\Paginate;
use Helpers\Sort;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    /**
     * @throws QueryException
     */
    #[Route('/products', name: 'products')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $rates = $em->getRepository(ExchangeRate::class)->findAll();

        // ****** Life exchange rate reload.
        //
        //        if(!$request->get('exchange')){
        //            return $this->redirectToRoute('app_exchange_rate');
        //        }

        $query = $em->getRepository(Product::class)->createQueryBuilder('d');
        if($request->get('sortType')) {
            $query = Sort::sorting(
                $request->get('sortType'),
                $request->get('sortItemName'),
                $query
            );
        }

        if($request->get('filterType') || $request->get('name')){
            $query = Filter::filtering(
                $query,
                $request->get('filterType', ''),
                $request->get('name', ''),
            );
        }

        $pagination = new Paginate($query, $request);
        $products = $pagination->paginate($query, $request);

        return $this->render('index.html.twig', array(
            "products"=>$products,
            "customers"=>$em->getRepository(Customer::class)->findAll(),
            "lastPage" => $pagination->lastPage($products),
            "rates"=>$rates
        ));
    }

    #[Route('/products/{id}', name: 'product')]
    public function show(EntityManagerInterface $em, $id, Request $request): Response
    {
        $rates = $em->getRepository(ExchangeRate::class)->findAll();
        if(!$request->get('exchange')){
            return $this->redirectToRoute('app_exchange_rate', array('id'=>$id));
        }
        $id = $request->get('id');
        $repository = $em->getRepository(Product::class);
        $product = $repository->find($id);

        return $this->render('show.html.twig', array(
            "product"=>$product,
            "id"=>$id,
            "rates"=>$rates
        ));
    }

    #[Route('/menu/product/add', name: 'product/add')]
    public function addProduct(EntityManagerInterface $em, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $customers = $em->getRepository(Customer::class)->findAll();
        $services = $em->getRepository(Service::class)->findAll();

        if($request->request->all()) {
            $product = new Product();
            $product->setName($request->request->get('name'));
            $product->setCost($request->request->get('cost'));
            $product->setDescription($request->request->get('description'));
            $product->setReleaseDate(new \DateTime());
            $product->setCustomer($em->getRepository(Customer::class)->find($request->request->get('customer')));

            if($request->request->all()['checkboxes']) {
                $repository = $em->getRepository(Service::class);
                $i = 0;
                while ($i < count($request->request->all()['checkboxes'])){
                    $service = $repository->find($request->request->all()['checkboxes'][$i]);
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

        $query = $em->getRepository(Product::class)->createQueryBuilder('d');
        $pagination = new Paginate($query, $request);
        $products = $pagination->paginate($query, $request);

        if($request->request->all()) {
            $i = 0;
            foreach ($products as $product){
                $product->setName($request->request->all()['product-name'][$i]);
                $product->setCost($request->request->all()['product-cost'][$i]);
                $product->setDescription($request->request->all()['product-description'][$i]);
                $time = DateTime::createFromFormat('d/m/Y',$request->request->all()['product-release'][$i]);
                $product->setReleaseDate($time);
                $product->setCustomer($em->getRepository(Customer::class)->find($request->request->all()['product-customer'][$i]));
                $product->clearServices();
                if($request->request->all()['product-checkboxes']) {
                    $keys = array_keys($request->request->all()['product-checkboxes']);
                        foreach ($keys as $key) {
                            if ($product->getId() == $key) {
                                foreach ($request->request->all()['product-checkboxes']["$key"] as $serviceId) {
                                    $service = $em->getRepository(Service::class)->find($serviceId);
                                    $product->addService($service);
                                }
                            }
                        }
                }
                $i++;
            }
            $em->flush();
            return $this->render('products/edit.html.twig', array("status"=>true, "customers"=>$customers, "services"=>$services, "products"=>$products, 'lastPage' => $pagination->lastPage($products)));
        }
        else {
            return $this->render('products/edit.html.twig', array("status"=>false, "customers"=>$customers, "services"=>$services, "products"=>$products, 'lastPage' => $pagination->lastPage($products)));
        }
    }
    #[Route('/menu/product/delete', name: 'product/delete')]
    public function deleteProduct(EntityManagerInterface $em, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $repository = $em->getRepository(Product::class);
        $query = $em->getRepository(Product::class)->createQueryBuilder('d');
        $pagination = new Paginate($query, $request);
        $products = $pagination->paginate($query, $request);

        if($request->request->all()) {
            $i = 0;
            while ($i < count($request->request->all()['checkboxes'])){
                $product = $repository->find($request->request->all()['checkboxes'][$i]);
                $em->remove($product);
                $i++;
            }
            $em->flush();
        }
        return $this->render('products/delete.html.twig', array(
            "products"=>$products,
            'lastPage' => $pagination->lastPage($products)
        ));
    }
    #[Route('/menu/product/save', name: 'product/save')]
    public function save(EntityManagerInterface $em): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        AwsServices::init($em->getRepository(\App\Entity\Product::class)->findAll());
        return $this->redirectToRoute('menu');
    }
}

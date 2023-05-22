<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Paginate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    #[Route('/menu/customer/add', name: 'customer/add')]
    public function addCustomer(EntityManagerInterface $em, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        if($request->request->get('name') !== null) {
            $customer = new Customer();
            $customer->setName($request->request->get('name'));
            $em->persist($customer);
            $em->flush();
            return $this->render('customers/add.html.twig', array("status"=>true));
        }
        else {
            return $this->render('customers/add.html.twig', array("status"=>false));
        }

    }

    #[Route('/menu/customer/edit', name: 'customer/edit')]
    public function editCustomer(EntityManagerInterface $em, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $query = $em->getRepository(Customer::class)->createQueryBuilder('d');
        $pagination = new Paginate($query, $request);
        $customers = $pagination->paginate($query, $request);

        if($request->request->all()) {
            $i = 0;
            foreach ($customers as $customer){
                $customer->setName($request->request->all()['customer-name'][$i]);
                $i++;
            }
            $em->flush();
        }
        return $this->render('customers/edit.html.twig', array(
            "customers"=>$customers,
            'lastPage' => $pagination->lastPage($customers)
        ));
    }

    #[Route('/menu/customer/delete', name: 'customer/delete')]
    public function deleteCustomer(EntityManagerInterface $em, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $repository = $em->getRepository(Customer::class);
        $query = $em->getRepository(Customer::class)->createQueryBuilder('d');
        $pagination = new Paginate($query, $request, Paginate::$ITEMS_PER_PAGE);
        $customers = $pagination->paginate($query, $request, Paginate::$ITEMS_PER_PAGE);

        if($request->request->all()) {
            $i = 0;
            while ($i < count($request->request->all()['checkboxes'])){
                $customer = $repository->find($request->request->all()['checkboxes'][$i]);
                $em->remove($customer);
                $i++;
            }
            $em->flush();
        }
        return $this->render('customers/delete.html.twig', array(
            "customers"=>$customers,
            'lastPage' => $pagination->lastPage($customers)
        ));
    }
}
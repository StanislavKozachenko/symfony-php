<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $repository = $em->getRepository(Customer::class);
        $customers = $repository->findAll();
        if($request->request->get('customer-name') !== null) {
            $i = 0;
            foreach ($customers as $customer){
                $customer->setName($request->request->get('customer-name')[$i]);
                $i++;
            }
            $em->flush();
        }
        return $this->render('customers/edit.html.twig', array(
            "customers"=>$customers
        ));
    }

    #[Route('/menu/customer/delete', name: 'customer/delete')]
    public function deleteCustomer(EntityManagerInterface $em, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $repository = $em->getRepository(Customer::class);
        $customers = $repository->findAll();
        if($request->request->get('checkboxes') !== null) {
            $i = 0;
            while ($i < $request->request->get('checkboxes')){
                $customer = $repository->find($request->request->get('checkboxes')[$i]);
                $em->remove($customer);
                $i++;
            }
            $em->flush();
        }
        return $this->render('customers/delete.html.twig', array(
            "customers"=>$customers
        ));
    }
}
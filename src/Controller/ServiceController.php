<?php

namespace App\Controller;

use App\Entity\Service;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class ServiceController extends AbstractController
{
    #[Route('/menu/service/add', name: 'service/add')]
    public function addService(EntityManagerInterface $em): \Symfony\Component\HttpFoundation\Response
    {
        if(isset($_POST['name']) && isset($_POST['cost'])) {
            $service = new Service();
            $service->setName($_POST['name']);
            $service->setCost($_POST['cost']);
            $service->setReleaseDate(new \DateTime());
            $em->persist($service);
            $em->flush();
            return $this->render('services/add.html.twig', array("status"=>true));
        }
        else {
            return $this->render('services/add.html.twig', array("status"=>false));
        }

    }

    #[Route('/menu/service/edit', name: 'service/edit')]
    public function editService(EntityManagerInterface $em): \Symfony\Component\HttpFoundation\Response
    {
        $repository = $em->getRepository(Service::class);
        $services = $repository->findAll();
        if(isset($_POST['service-name']) && isset($_POST['service-cost']) && isset($_POST['service-release'])) {
            $i = 0;
            foreach ($services as $service){
                $service->setName($_POST['service-name'][$i]);
                $service->setCost($_POST['service-cost'][$i]);
                $time = DateTime::createFromFormat('d/m/Y', $_POST['service-release'][$i]);
                $service->setReleaseDate($time);
                $i++;
            }
            $em->flush();
        }
        return $this->render('services/edit.html.twig', array(
            "services"=>$services
        ));
    }

    #[Route('/menu/service/delete', name: 'service/delete')]
    public function deleteService(EntityManagerInterface $em): \Symfony\Component\HttpFoundation\Response
    {
        $repository = $em->getRepository(Service::class);
        $services = $repository->findAll();
        if(isset($_POST['checkboxes'])) {
            $i = 0;
            while ($i < count($_POST['checkboxes'])){
                $service = $repository->find($_POST['checkboxes'][$i]);
                $em->remove($service);
                $i++;
            }
            $em->flush();
        }
        return $this->render('services/delete.html.twig', array(
            "services"=>$services
        ));
    }
}
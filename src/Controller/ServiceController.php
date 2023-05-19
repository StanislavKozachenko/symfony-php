<?php

namespace App\Controller;

use App\Entity\Service;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class ServiceController extends AbstractController
{
    #[Route('/menu/service/add', name: 'service/add')]
    public function addService(EntityManagerInterface $em, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        if($request->request->all()) {
            $service = new Service();
            $service->setName($request->request->get('name'));
            $service->setCost($request->request->get('cost'));
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
    public function editService(EntityManagerInterface $em, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $repository = $em->getRepository(Service::class);
        $services = $repository->findAll();
        if($request->request->all()) {
            $i = 0;
            foreach ($services as $service){
                $service->setName($request->request->all()['service-name'][$i]);
                $service->setCost($request->request->all()['service-cost'][$i]);
                $time = DateTime::createFromFormat('d/m/Y', $request->request->all()['service-release'][$i]);
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
    public function deleteService(EntityManagerInterface $em, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $repository = $em->getRepository(Service::class);
        $services = $repository->findAll();
        if($request->request->all()) {
            $i = 0;
            while ($i < count($request->request->all()['checkboxes'])){
                $service = $repository->find($request->request->all()['checkboxes'][$i]);
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
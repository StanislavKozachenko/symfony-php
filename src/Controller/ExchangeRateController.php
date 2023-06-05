<?php

namespace App\Controller;

use App\Entity\ExchangeRate;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRateController extends AbstractController
{

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    #[Route('/exchange/rate', name: 'app_exchange_rate')]
    public function index(EntityManagerInterface $em, HttpClientInterface $client, Request $request): Response
    {
        $response = $client->request(
            'GET',
            'https://bankdabrabyt.by/export_courses.php'
        );
        $array = (array)simplexml_load_string($response->getContent());
        $time = $array['time'];
        $bank = $array['filials'];

        $exchangeRate = new ExchangeRate();

        $exchangeRate->setTime(new \DateTime($time));
        $exchangeRate->setUsd(reset((reset($bank)['0'])->rates->value[0]['sale']));
        $exchangeRate->setEur(reset((reset($bank)['0'])->rates->value[1]['sale']));
        $exchangeRate->setRub(reset((reset($bank)['0'])->rates->value[2]['sale']));

        $rates = $em->getRepository(ExchangeRate::class)->findAll();
        foreach ($rates as $entity) {
            $em->remove($entity);
        }
        $em->persist($exchangeRate);
        $em->flush();

        if($request->get('id')){
            return $this->redirectToRoute('product',
                array(
                    'id'=>$request->get('id'),
                    'exchange'=>true
                ));
        } else {
            return $this->redirectToRoute('products',
                array('exchange'=>true
                ));
        }
    }
}

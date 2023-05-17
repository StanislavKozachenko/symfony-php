<?php

namespace App\Controller;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/menu', name: 'menu')]
    public function show(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('user/user.html.twig');
    }

    #[Route('/select', name: 'select')]
    public function select(): \Symfony\Component\HttpFoundation\Response
    {
        $selected_value = $_POST['admin_select'];
        return $this->redirectToRoute($selected_value);
    }
}
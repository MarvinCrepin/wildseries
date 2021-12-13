<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
     /**
     * @Route("/profile", name="app_profile")
     *
     * @IsGranted("ROLE_CONTRIBUTOR")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }
}

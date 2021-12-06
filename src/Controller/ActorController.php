<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActorController extends AbstractController
{

    /**
     * @Route("/actor/{id}", name="actor_show")
     */

    public function show(Actor $actor, ProgramRepository $programRepository): Response
    {
        $program = $actor->getPrograms();
        
        return $this->render('actor/show.html.twig', [
            'actor' => $actor, 'programs' => $program
        ]);
    }
}

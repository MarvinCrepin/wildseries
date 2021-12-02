<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Form\SeriesType;
use App\Entity\Program;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        return $this->render('default/index.html.twig', ['programs' => $programs,]);
    }

     /**
     * @Route("/add", name="app_new")
     */

    public function new(Request $request): Response
    {
        $seriesform = new Program();
        $seriesform->setCategory(null);
        // ...

        $form = $this->createForm(SeriesType::class, $seriesform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($seriesform);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_index', [
                'id' => $seriesform->getId()
            ]);
        }

        return $this->renderForm('default/new.html.twig', [
            'form' => $form,
        ]);
    }
    
}

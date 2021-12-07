<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Repository\ProgramRepository;
use App\Repository\EpisodeRepository;
use App\Repository\SeasonRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SeriesType;



class ProgramController extends AbstractController
{

    /**
     * @Route("/program/", name="program_index")
     */

    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        return $this->render(
            'program/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * @Route("/program/list/{page}", requirements={"page"="\d+"}, name="program_list")
     */
    public function list(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        return $this->render('program/list.html.twig', ['programs' => $programs,]);
    }

    /**
     * @Route("/program/{id<^[0-9]+$>}", requirements={"id"="\d+"}, name="program_show")
     */
    public function show(Program $program, SeasonRepository $seasonRepository): Response
    {   

        $seasons = $seasonRepository->findByProgram($program);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . 'id' . ' found in program\'s table.'
            );
        }

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }
    /**
     * @Route("/program/{program}/season/{season}", name="program_season_show")
     */

    public function showSeason(Program $program, Season $season, EpisodeRepository $episodeRepository): Response
    {
        $episode = $episodeRepository->findBySeason($season);

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episode
        ]);
    }

     /**
     * @Route("/program/{programId}/season/{seasonId}/episode/{episodeId}", name="program_episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episodeId": "id"}})
     */

    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episode
        ]);
    }

     /**
     * @Route("/program/new", name="app_new_program")
     */

    public function new(Request $request): Response
    {
        
        $seriesform = new Program();
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

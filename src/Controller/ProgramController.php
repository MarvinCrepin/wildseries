<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Repository\ProgramRepository;
use App\Repository\EpisodeRepository;
use App\Repository\SeasonRepository;

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

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program['id'] . ' found in program\'s table.'
            );
        }

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episode
        ]);
    }

     /**
     * @Route("/program/{program}/season/{season}/episode/{episode}", name="program_episode_show")
     */

    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program['id'] . ' found in program\'s table.'
            );
        }

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episode
        ]);
    }
}

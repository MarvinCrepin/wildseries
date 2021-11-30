<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProgramRepository;
use App\Entity\Program;
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
    public function show(int $id, ProgramRepository $programRepository, SeasonRepository $seasonsRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $id]);

        $seasons = $seasonsRepository->findByExampleField($id);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $id . ' found in program\'s table.'
            );
        }

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }
    /**
     * @Route("/program/{programId}/seasons/{seasonId}", name="program_season_show")
     */
    public function showSeason(int $programId, int $seasonId, EpisodeRepository $episodeRepository,  ProgramRepository $programRepository, SeasonRepository $seasonsRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $programId]);

        $season = $seasonsRepository->findOneBySomeField($seasonId);

        $episode = $episodeRepository->findByExampleField($seasonId);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $programId . ' found in program\'s table.'
            );
        }

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episode
        ]);
    }
}

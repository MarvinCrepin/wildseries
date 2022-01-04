<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\CommentType;
use App\Repository\ProgramRepository;
use App\Repository\EpisodeRepository;
use App\Repository\SeasonRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProgramType;
use App\Form\SearchProgramFormType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

class ProgramController extends AbstractController
{

    /**
     * @Route("/program/", name="program_index")
     */


    // ProgramController

    public function index(Request $request, ProgramRepository $programRepository): Response
    {
        $form = $this->createForm(SearchProgramFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $programs = $programRepository->findLikeName($search);
        } else {
            $programs = $programRepository->findAll();
        }

        return $this->render('program/index.html.twig', [
            'programs' => $programs,
            'form' => $form->createView(),
        ]);
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

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
        ]);
    }

    /**
     * @Route("/program/{programId}/season/{seasonId}/episode/{episodeId}", name="program_episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episodeId": "id"}})
     */

    public function showEpisode(CommentRepository $commentRepository, Request $request, EntityManagerInterface $entityManager, Program $program, Season $season, Episode $episode): Response
    {
        $this->forward("App\Controller\CommentController::new", [
            "episode" => $episode,
            "request" => $request,
            "entityManager" => $entityManager,
        ]);

        return $this->render('program/episode_show.html.twig', [
            'comments' => $commentRepository->findByEpisode($episode, ['id' => 'ASC']),
            'program' => $program,
            'season' => $season,
            'episodes' => $episode
        ]);
    }

    /**
     * @Route("/program/new", name="app_new_program")
     *
     * @IsGranted("ROLE_ADMIN")
     */

    public function new(Request $request): Response
    {
        $seriesform = new Program();

        $form = $this->createForm(ProgramType::class, $seriesform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $seriesform->setOwner($user);
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

    #[Route('program/{id}/edit', name: 'program_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($user !== $program->getOwner() && $user->getRoles()[0] !== 'ROLE_ADMIN') {
            // If not the owner, throws a 403 Access Denied exception
            throw new Exception('Only the owner can edit the program!');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('program/{id}', name: 'program_delete', methods: ['POST'])]
    public function delete(Request $request, program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $program->getId(), $request->request->get('_token'))) {
            $entityManager->remove($program);
            $entityManager->flush();
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }
}

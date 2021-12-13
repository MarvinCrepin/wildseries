<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\EpisodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'comment_index', methods: ['GET'])]
    public function index(EpisodeRepository $episodeRepository, CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
            'episodes' => $episodeRepository->findAll()
        ]);
    }


    public function new(Episode $episode, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setEpisode($episode)->setUser($user);
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->renderForm('comment/new.html.twig', [
            'form' => $form,
            'episode' => $episode,
        ]);
    }

    #[Route('/{id}', name: 'comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($this->getUser() !== $comment->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Tu dois être administrateur ou avoir posté le commentaire');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete")
     * 
     * @IsGranted("ROLE_CONTRIBUTOR")
     */

    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $comment->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Tu dois être administrateur ou avoir posté le commentaire');
        }

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);            

    }
}

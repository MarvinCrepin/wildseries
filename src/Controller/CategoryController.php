<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;

class CategoryController extends AbstractController
{

    public function __construct(ProgramRepository $ProgramRepository)
    {
        $this->repository = $ProgramRepository;
    }

    /**
     * @Route("/category/", name="category_index")
     */

    public function index(): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render(
            'category/index.html.twig',
            ['categories' => $categories]
        );
    }
    /**
     * @Route("/category/{categoryName}", requirements={"id"="\d+"}, name="category_show")
     */
    public function show(string $categoryName, CategoryRepository $categoryRepository, ProgramRepository $programRepository): Response
    {   

        $category = $categoryRepository->findByName($categoryName);

        if (!$category) {
            throw $this->createNotFoundException(
                'No category with name : ' . $categoryName . ' found in category\'s table.'
            );
        } else {
            $programs = $programRepository->findByCategory($category, ['id' => 'desc'], '3');
        }

        return $this->render('category/show.html.twig', [
            'programs' => $programs,
            'category' => $categoryName
        ]);
    }
    /**
     * @Route("/new/category", name="app_new_category")
     */

    public function new(Request $request): Response
    {
        $categoryForm = new Category();
        // ...

        $form = $this->createForm(CategoryType::class, $categoryForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categoryForm);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_index', [
                'id' => $categoryForm->getId()
            ]);
        }

        return $this->renderForm('default/new.html.twig', [
            'form' => $form,
        ]);
    }
}

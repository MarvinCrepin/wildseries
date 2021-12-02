<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;

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
            $programs = $programRepository->findByCategory($category);
        }

        return $this->render('category/show.html.twig', [
            'programs' => $programs,
            'category' => $categoryName
        ]);
    }
}

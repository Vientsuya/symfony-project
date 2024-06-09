<?php

namespace App\Controller;

use App\Service\CategoryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

/**
 *  Class CategoryController.
 */
#[Route('/category')]
class CategoryController extends AbstractController
{
    /**
     * Constructor.
     */
    public function __construct(private readonly CategoryServiceInterface $categoryService)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(name: 'category_index', methods: 'GET')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->categoryService->getPaginatedList($page);

        return $this->render('category/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * @param string $categoryName
     * @param int $page
     * @return Response
     */
    #[Route('/{categoryName}', name: 'show_task_list', methods: 'GET')]
    public function showTaskList(string $categoryName, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->categoryService->getTasksOfCategory($categoryName, $page);

        return $this->render('category/show_task_list.html.twig', ['pagination' => $pagination, 'category' => $categoryName]);
    }
}

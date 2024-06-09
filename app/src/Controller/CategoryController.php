<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    /**
     * Index action.
     *
     * @param CategoryRepository $categoryRepository Category repository
     * @param PaginatorInterface $paginator          Paginator
     *
     * @return Response HTTP response
     */
    #[Route(name: 'category_index', methods: 'GET')]
    public function index(CategoryRepository $categoryRepository, PaginatorInterface $paginator, #[MapQueryParameter]
        int $page = 1): Response
    {
        $pagination = $paginator->paginate(
            $categoryRepository->queryAll(),
            $page,
            CategoryRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render('category/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Index action.
     *
     * @param TaskRepository     $taskRepository Task repository
     * @param PaginatorInterface $paginator      Paginator
     *
     * @return Response HTTP response
     */
    #[Route('/{category}', name: 'show_task_list', methods: 'GET')]
    public function showTaskList(
        TaskRepository $taskRepository,
        PaginatorInterface $paginator,
        string $category,
        #[MapQueryParameter]
        int $page = 1
    ): Response {
        $pagination = $paginator->paginate(
            $taskRepository->queryByCategory($category),
            $page,
            TaskRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render('category/show_task_list.html.twig', ['pagination' => $pagination, 'category' => $category]);
    }
}

<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category_index')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    /**
     * Index action.
     *
     * @param TaskRepository     $taskRepository Task repository
     * @param PaginatorInterface $paginator      Paginator
     *
     * @return Response HTTP response
     */
    #[Route('category/{category}', name: 'show_task_list', methods: 'GET')]
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

        return $this->render('task/index.html.twig', ['pagination' => $pagination]);
    }
}

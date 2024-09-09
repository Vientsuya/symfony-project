<?php
/**
 * Comment service.
 */

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Task;
use App\Repository\CommentRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CommentService.
 */
class CommentService implements CommentServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param CommentRepository  $commentRepository Task repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(
        private readonly CommentRepository $commentRepository,
        private readonly TaskRepository $taskRepository,
        private readonly PaginatorInterface $paginator
    ) {
    }

    /**
     * Save entity.
     *
     * @param Comment $comment Comment entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Comment $comment): void
    {
        $this->commentRepository->save($comment);
    }

    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->commentRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get paginated tasks for a specific user.
     *
     * @param int $taskId the ID of the task whose comments are being retrieved
     * @param int $page   the page number to retrieve
     *
     * @return PaginationInterface the paginated list of tasks for the user
     *
     * @throws \InvalidArgumentException if the user with the given ID does not exist
     */
    public function getTaskComments(int $taskId, int $page): PaginationInterface
    {
        $task = $this->taskRepository->find($taskId);

        return $this->paginator->paginate(
            $this->commentRepository->queryByTask($task),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }
}

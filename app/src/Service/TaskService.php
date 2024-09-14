<?php
/**
 * Task service.
 */

namespace App\Service;

use App\Entity\Task;
use App\Repository\CommentRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class TaskService.
 */
class TaskService implements TaskServiceInterface
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
    private const PAGINATOR_ITEMS_PER_PAGE = 12;

    /**
     * Constructor.
     *
     * @param TaskRepository     $taskRepository    Task repository
     * @param UserRepository     $userRepository    User Repository
     * @param CommentRepository  $commentRepository Comment Repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(private readonly TaskRepository $taskRepository, private readonly UserRepository $userRepository, private readonly CommentRepository $commentRepository, readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Save entity.
     *
     * @param Task $task Task entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Task $task): void
    {
        $this->taskRepository->save($task);
    }

    /**
     * Delete entity.
     *
     * @param Task $task Task entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Task $task): void
    {
        $comments = $this->commentRepository->queryByTask($task)->getQuery()->getResult();

        foreach ($comments as $comment) {
            $this->commentRepository->delete($comment);
        }

        $this->taskRepository->delete($task);
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
            $this->taskRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get paginated tasks for a specific user.
     *
     * @param int $userId the ID of the user whose tasks are being retrieved
     * @param int $page   the page number to retrieve
     *
     * @return PaginationInterface the paginated list of tasks for the user
     *
     * @throws \InvalidArgumentException if the user with the given ID does not exist
     */
    public function getUserTasks(int $userId, int $page): PaginationInterface
    {
        $user = $this->userRepository->find($userId);

        return $this->paginator->paginate(
            $this->taskRepository->queryByAuthor($user),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get tasks given an id.
     *
     * @param int $id Id
     *
     * @return Task Task
     */
    public function getTaskById(int $id): Task
    {
        return $this->taskRepository->find($id);
    }
}

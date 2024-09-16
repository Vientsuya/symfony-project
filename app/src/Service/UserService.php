<?php

/**
 * User service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
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
     * @param UserRepository     $userRepository    User Repository
     * @param TaskRepository     $taskRepository    Task Repository
     * @param CommentRepository  $commentRepository Comment Repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly TaskRepository $taskRepository, private readonly CommentRepository $commentRepository, private readonly PaginatorInterface $paginator)
    {
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
            $this->userRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param User $user User entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Delete entity.
     *
     * @param User $user User entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(User $user): void
    {
        $tasks = $this->taskRepository->queryByAuthor($user)->getQuery()->getResult();

        foreach ($tasks as $task) {
            $comments = $this->commentRepository->queryByTask($task)->getQuery()->getResult();
            foreach ($comments as $comment) {
                $this->commentRepository->delete($comment);
            }
        }

        $comments = $this->commentRepository->queryByAuthor($user)->getQuery()->getResult();
        foreach ($comments as $comment) {
            $this->commentRepository->delete($comment);
        }

        foreach ($tasks as $task) {
            $this->taskRepository->delete($task);
        }

        $this->userRepository->delete($user);
    }

    /**
     * @param string $email Email
     *
     * @return User|null User object or null
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }
}

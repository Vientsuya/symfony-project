<?php
/**
 * Task service interface.
 */

namespace App\Service;

use App\Entity\Task;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface TaskServiceInterface.
 */
interface TaskServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Task $task Task entity
     */
    public function save(Task $task): void;

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
    public function getUserTasks(int $userId, int $page): PaginationInterface;
}
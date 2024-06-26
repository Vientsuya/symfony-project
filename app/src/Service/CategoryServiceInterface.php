<?php
/**
 * Category service interface.
 */

namespace App\Service;

use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface CategoryServiceInterface.
 */
interface CategoryServiceInterface
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
     * @param string $categoryName
     * @param int $page
     * @return PaginationInterface
     */
    public function getTasksOfCategory(string $categoryName, int $page): PaginationInterface;
}

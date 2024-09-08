<?php

/**
 * User service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    /**
     * @param UserRepository $userRepository
     */
    public function __construct(private readonly UserRepository $userRepository)
    {
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
        $this->userRepository->delete($user);
    }
}

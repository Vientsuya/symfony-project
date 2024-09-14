<?php
/**
 * Task voter.
 */

namespace App\Security\Voter;

use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TaskVoter.
 */
class TaskVoter extends Voter
{
    /**
     * Edit permission.
     *
     * @const string
     */
    private const EDIT = 'EDIT';

    /**
     * View permission.
     *
     * @const string
     */
    private const VIEW = 'VIEW';

    /**
     * Delete permission.
     *
     * @const string
     */
    private const DELETE = 'DELETE';

    /**
     * Create comment permission.
     *
     * @const string
     */
    private const CREATE_COMMENT = 'CREATE_COMMENT';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE, self::CREATE_COMMENT])
            && $subject instanceof Task;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     *
     * @return bool Vote result
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        if (!$subject instanceof Task) {
            return false;
        }

        // Grant full access to users with the "ADMIN" role
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::VIEW => $this->canView($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            self::CREATE_COMMENT => $this->canCreateComment($token),
            default => false,
        };
    }

    /**
     * Checks if user can edit task.
     *
     * @param Task          $task Task entity
     * @param UserInterface $user User *
     *
     * @return bool Result
     */
    private function canEdit(Task $task, UserInterface $user): bool
    {
        return $task->getAuthor() === $user;
    }

    /**
     * Checks if user can view task.
     *
     * @param Task          $task Task entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canView(Task $task, UserInterface $user): bool
    {
        return true;
    }

    /**
     * Checks if user can delete task.
     *
     * @param Task          $task Task entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canDelete(Task $task, UserInterface $user): bool
    {
        return $task->getAuthor() === $user;
    }

    /**
     * Checks if user can create comment.
     *
     * @param TokenInterface $token Token interface
     *
     * @return bool Result
     */
    private function canCreateComment(TokenInterface $token): bool
    {
        return $token->getUser() instanceof UserInterface;
    }
}

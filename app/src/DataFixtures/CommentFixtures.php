<?php
/**
 * Comment Fixtures
 */

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CommentFixtures.
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullPropertyFetch
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(100, 'comments', function (int $i) {
            $comment = new Comment();
            $comment->setContent($this->faker->sentence);
            $createdAt = \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $comment->setCreatedAt($createdAt);
            $comment->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween($createdAt->format('Y-m-d H:i:s'), '-1 days')
                )
            );
            /** @var Task $category */
            $task = $this->getRandomReference('tasks');
            $comment->setTask($task);

            /** @var User $author */
            $author = $this->getRandomReference('users');
            $comment->setAuthor($author);

            return $comment;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: TaskFixtures::class, 1: UserFixtures::class}
     */
    public function getDependencies(): array
    {
        return [TaskFixtures::class, UserFixtures::class];
    }
}

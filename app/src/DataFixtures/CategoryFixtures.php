<?php
/**
 * Category fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;

/**
 * Class CategoryFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class CategoryFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        $this->createMany(20, 'categories', function (int $i) {
            $category = new Category();
            $category->setTitle($this->faker->unique()->word);
            $createdAt = \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $category->setCreatedAt($createdAt);
            $category->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween($createdAt->format('Y-m-d H:i:s'), '-1 days')
                )
            );

            return $category;
        });

        $this->manager->flush();
    }
}
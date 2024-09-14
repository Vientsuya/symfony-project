<?php

namespace App\Entity;

use App\Repository\ThumbnailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Thumbnail.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: ThumbnailRepository::class)]
#[ORM\Table(name: 'thumbnails')]
#[ORM\UniqueConstraint(name: 'uq_thumbnails_filename', columns: ['fileName'])]
#[UniqueEntity(fields: ['fileName'])]
class Thumbnail
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Filename.
     */
    #[ORM\Column(name: 'fileName', type: 'string', length: 191)]
    #[Assert\Type('string')]
    private ?string $fileName = null;

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for filename.
     *
     * @return string|null Filename
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * Setter for filename.
     *
     * @param string $fileName
     * @return Thumbnail
     */
    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }
}

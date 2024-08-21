<?php
/**
 * Thumbnail service interface.
 */

namespace App\Service;

use App\Entity\Task;
use App\Entity\Thumbnail;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Thumbnail service.
 */
interface ThumbnailServiceInterface
{
    /**
     * Create thumbnail.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Thumbnail    $thumbnail    Thumbnail entity
     * @param Task         $task         Task entity
     */
    public function create(UploadedFile $uploadedFile, Thumbnail $thumbnail, Task $task): void;

    /**
     * Update avatar.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Thumbnail    $thumbnail    Thumbnail entity
     * @param Task         $task         Task
     */
    public function update(UploadedFile $uploadedFile, Thumbnail $thumbnail, Task $task): void;
}

<?php
/**
 * Thumbnail service.
 */

namespace App\Service;

use App\Entity\Task;
use App\Entity\Thumbnail;
use App\Repository\ThumbnailRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ThumbnailService.
 */
class ThumbnailService implements ThumbnailServiceInterface
{
    /**
     * Constructor.
     *
     * @param string                     $targetDirectory     Target directory
     * @param ThumbnailRepository        $thumbnailRepository Thumbnail repository
     * @param FileUploadServiceInterface $fileUploadService   File upload service
     * @param Filesystem                 $filesystem          Filesystem component
     */
    public function __construct(private readonly string $targetDirectory, private readonly ThumbnailRepository $thumbnailRepository, private readonly FileUploadServiceInterface $fileUploadService, private readonly Filesystem $filesystem)
    {
    }

    /**
     * Update thumbnail.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Thumbnail    $thumbnail    Thumbnail entity
     * @param Task         $task         Task entity
     */
    public function update(UploadedFile $uploadedFile, Thumbnail $thumbnail, Task $task): void
    {
        $filename = $thumbnail->getFilename();

        if (null !== $filename) {
            $this->filesystem->remove(
                $this->targetDirectory.'/'.$filename
            );

            $this->create($uploadedFile, $thumbnail, $task);
        }
    }

    /**
     * Create avatar.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Thumbnail    $thumbnail    Thumbnail entity
     * @param Task         $task         Task entity
     */
    public function create(UploadedFile $uploadedFile, Thumbnail $thumbnail, Task $task): void
    {
        $thumbnailFilename = $this->fileUploadService->upload($uploadedFile);

        $thumbnail->setTask($task);
        $thumbnail->setFilename($thumbnailFilename);
        $this->thumbnailRepository->save($thumbnail);
    }
}

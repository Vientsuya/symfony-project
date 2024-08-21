<?php
/**
 * Thumbnail controller.
 */

namespace App\Controller;

use App\Entity\Thumbnail;
use App\Form\Type\ThumbnailType;
use App\Repository\TaskRepository;
use App\Service\ThumbnailServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ThumbnailController.
 */
#[Route('/thumbnail')]
class ThumbnailController extends AbstractController
{
    private TaskRepository $taskRepository;

    /**
     * Constructor.
     *
     * @param ThumbnailServiceInterface $thumbnailService Thumbnail service
     * @param TranslatorInterface       $translator       Translator
     */
    public function __construct(private readonly ThumbnailServiceInterface $thumbnailService, private readonly TranslatorInterface $translator, TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create/{taskId}',
        name: 'thumbnail_create',
        requirements: ['taskId' => '[1-9]\d*'],
        methods: 'GET|POST'
    )]
    public function create(Request $request, int $taskId): Response
    {
        $task = $this->taskRepository->find($taskId);

        if ($task->getThumbnail()) {
            return $this->redirectToRoute(
                'thumbnail_edit',
                ['id' => $task->getThumbnail()->getId(), 'taskId' => $taskId]
            );
        }

        $thumbnail = new Thumbnail();
        $form = $this->createForm(
            ThumbnailType::class,
            $thumbnail,
            ['action' => $this->generateUrl('thumbnail_create', ['taskId' => $taskId])]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $this->thumbnailService->create(
                $file,
                $thumbnail,
                $task
            );

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'thumbnail/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request   $request   HTTP request
     * @param Thumbnail $thumbnail Thumbnail entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/edit/{taskId}',
        name: 'thumbnail_edit',
        requirements: ['id' => '[1-9]\d*', 'taskId' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, Thumbnail $thumbnail, int $taskId): Response
    {
        $task = $this->taskRepository->find($taskId);
        if (!$task->getThumbnail()) {
            return $this->redirectToRoute('thumbnail_create');
        }

        $form = $this->createForm(
            ThumbnailType::class,
            $thumbnail,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('thumbnail_edit', ['id' => $thumbnail->getId(), 'taskId' => $taskId]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $this->thumbnailService->update(
                $file,
                $thumbnail,
                $task
            );

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'thumbnail/edit.html.twig',
            [
                'form' => $form->createView(),
                'thumbnail' => $thumbnail,
            ]
        );
    }
}

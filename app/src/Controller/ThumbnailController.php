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
        methods: 'GET|POST'
    )]
    public function create(Request $request, int $taskId): Response
    {
        echo $taskId;
        $task = $this->taskRepository->find($taskId);

        if ($task->getThumbnail()) {
            return $this->redirectToRoute(
                'thumbnail_edit',
                ['id' => $taskId]
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
     * @param Request $request HTTP request
     * @param Avatar  $avatar  Avatar entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/edit',
        name: 'thumbnail_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, Avatar $avatar): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->getAvatar()) {
            return $this->redirectToRoute('avatar_create');
        }

        $form = $this->createForm(
            AvatarType::class,
            $avatar,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('avatar_edit', ['id' => $avatar->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $this->avatarService->update(
                $file,
                $avatar,
                $user
            );

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'avatar/edit.html.twig',
            [
                'form' => $form->createView(),
                'avatar' => $avatar,
            ]
        );
    }
}

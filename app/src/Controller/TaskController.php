<?php
/**
 * Task controller.
 */

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Thumbnail;
use App\Entity\User;
use App\Form\Type\TaskType;
use App\Service\TaskServiceInterface;
use App\Service\ThumbnailServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TaskController.
 */
#[Route('/task')]
class TaskController extends AbstractController
{
    /**
     * Constructor.
     */
    public function __construct(private readonly TaskServiceInterface $taskService, private
    readonly ThumbnailServiceInterface
    $thumbnailService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(name: 'task_index', methods: 'GET')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->taskService->getPaginatedList($page);

        return $this->render('task/main.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Task $task Task
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'task_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', ['task' => $task]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'task_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $task = new Task();
        $task->setAuthor($user);

        $thumbnail = new Thumbnail();
        $form = $this->createForm(
            TaskType::class,
            $task,
            ['action' => $this->generateUrl('task_create')]
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

            $task->setThumbnail($thumbnail);

            $this->taskService->save($task);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/create.html.twig',
            ['form' => $form->createView()]
        );
    }
}

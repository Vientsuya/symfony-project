<?php
/**
 * Admin Controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserAdminType;
use App\Service\CategoryService;
use App\Service\TaskServiceInterface;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AdminController.
 */
#[Route('/admin')]
class AdminController extends AbstractController
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly TaskServiceInterface $taskService,
        private readonly CategoryService $categoryService,
        private readonly UserServiceInterface $userService,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Index action.
     *
     * @return Response HTTP response
     */
    #[Route(name: 'admin_index', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * Manage tasks action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route('/tasks', name: 'admin_tasks', methods: 'GET')]
    public function manageTasks(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->taskService->getPaginatedList($page);

        return $this->render('admin/tasks.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Manage categories action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route('/categories', name: 'admin_categories', methods: 'GET')]
    public function manageCategories(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->categoryService->getPaginatedList($page);

        return $this->render('admin/categories.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Manage tasks action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route('/users', name: 'admin_users', methods: 'GET')]
    public function manageUsers(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->userService->getPaginatedList($page);

        return $this->render('admin/users.html.twig', ['pagination' => $pagination]);
    }

    /**
     * @param User $user User entity
     *
     * @return Response HTTP response
     */
    #[Route('/users/edit/{id}', name: 'admin_edit_user', methods: 'GET|PUT')]
    public function editUser(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
    {
        $redirectRoute = $request->query->get('redirect', 'admin_users');

        $form = $this->createForm(
            UserAdminType::class,
            null, // Pass null here, not the $user entity
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('admin_edit_user', ['id' => $user->getId(), 'redirect' => $redirectRoute]),
            ]
        );

        // Manually set the data for the form
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plaintextPassword = $form->get('password')->getData();

            if (!empty($plaintextPassword)) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $plaintextPassword
                );

                $user->setPassword($hashedPassword);
            }

            // Save the updated user entity
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute($redirectRoute);
        }

        return $this->render(
            'admin/user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * @param User $user User entity
     *
     * @return Response HTTP response
     */
    #[Route('/users/delete/{id}', name: 'admin_delete_user', methods: 'GET|DELETE')]
    public function deleteUser(Request $request, User $user): Response
    {
        $redirectRoute = $request->query->get('redirect', 'task_index');

        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('admin_delete_user', ['id' => $user->getId(), 'redirect' => $redirectRoute]),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->delete($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute($redirectRoute);
        }

        return $this->render(
            'admin/user/delete.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}

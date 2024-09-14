<?php
/**
 * Security Controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 *  Class SecurityController.
 */
class SecurityController extends AbstractController
{
    /**
     * @param UserServiceInterface $userService User Service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Register action,.
     *
     * @param Request                     $request        HTTP Request
     * @param UserPasswordHasherInterface $passwordHasher Password Hasher
     *
     * @return Response HTTP response
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $redirectRoute = $request->query->get('redirect', 'app_login');

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $plaintextPassword = $form->get('password')->getData();
            $role = 'ROLE_USER';

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );

            $user->setEmail($email);
            $user->setPassword($hashedPassword);
            $user->setRoles([$role]);

            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute($redirectRoute);
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Login action.
     *
     * @param AuthenticationUtils $authenticationUtils Authentication Utils Object
     *
     * @return Response HTTP Response
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Logout action.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Security\UserType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    #[Route('/admin/users', name: 'admin_users_index')]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();
        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/add', name: 'admin_users_add')]
    public function add(Request $request, UserPasswordHasherInterface $userPasswordHasher,): RedirectResponse|Response
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        if ($request->getMethod() === 'POST'){

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){

                $user->setPassword(
                  $userPasswordHasher->hashPassword($user,$form->get('password')->getData())
                );

                $this->userRepository->save($user, true);

                //TODO: send verification email

                $this->addFlash('success','admin.users.add.success');
                return $this->redirectToRoute('admin_users_index');
            }
        }


        return $this->render('admin/users/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

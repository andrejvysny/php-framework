<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\Security\PasswordChangeType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class AccountController extends AbstractController
{
    #[Route('/account', name: 'security_account')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(): Response
    {
        return $this->render('security/account/index.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }


    #[Route('/account/password-change', name: 'security_account_password_change')]
    public function passwordChange(Request $request, UserPasswordHasherInterface $hasher, UserRepository $userRepository): Response
    {

        $user = $this->getUser();
        assert($user instanceof User);

        $form = $this->createForm(PasswordChangeType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setPassword($hasher->hashPassword($user, $form->get('password')->getData()));

                $userRepository->save($user, true);

                $this->addFlash('success', 'security.account.password-change.flash.success');
                if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_default');
                }
                return $this->redirectToRoute('security_account');
            }
        }

        return $this->render(
            'security/account/password-change.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}

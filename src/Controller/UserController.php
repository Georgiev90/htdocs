<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use App\Form\Type\UserType;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class UserController extends AbstractController
{


    /**
     * @required
     */
    public EntityManagerInterface $entityManager;

    /**
     * @required
     */
    public NoteRepository $noteRepository;
    /**
     * @required
     */
    public UserRepository $userRepository;

    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = password_hash($form->getData()->getPassword(),PASSWORD_BCRYPT);
            $user->setPassword($password);

            // 4) save the User!
            $this->entityManager->persist($user);
            $this->entityManager->flush();


            return $this->redirectToRoute('login');
        }
        return $this->render('users/register.html.twig', ['form' => $form->createView()]);
    }

    public function login(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /**
             * @var User $user
             */
            $user = $this->userRepository->findOneBy(['username' => $form->getData()->getUsername()]);
            if ($user) {
                if (password_verify($form->getData()->getPassword(),$user->getPassword())) {

                    return $this->render('users/profile.html.twig', ['user' => $user]);
                }
                $this->addFlash('error', 'wrong password');
                return $this->renderView('users/login.html.twig', ['form' => $form]);
            }
            $this->addFlash('error', 'user not found');
        }

        return $this->render('users/login.html.twig', ['form' => $form->createView()]);

    }

    public function profile()
    {
        return $this->render('users/profile.html.twig', ['user' => $this->getUser()]);

    }

    public function logout()
    {
        unset($_SESSION);
        return $this->redirectToRoute('login');
    }
}
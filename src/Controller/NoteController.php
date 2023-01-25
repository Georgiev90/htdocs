<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use App\Form\Type\NoteType;
use App\Form\Type\UserType;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


class NoteController extends AbstractController
{
    /**
     * @required
     */
    public NoteRepository $noteRepository;

    /**
     * @required
     */
    public EntityManagerInterface $entityManager;
    /**
     * @required
     */
    public UserRepository $userRepository;

    public function create(Request $request)
    {
        $form = $this->createForm(NoteType::class, new Note());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var Note $note
             */
            $note = $form->getData();
            $note->setUser($this->getUser());
            $this->entityManager->persist($note);
            $this->entityManager->flush();
            return $this->redirectToRoute('profile');
        }
        return $this->render('notes/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    public function delete($id)
    {
        $note = $this->noteRepository->find($id);
        $this->entityManager->remove($note);
        $this->entityManager->flush();
        return $this->redirectToRoute('profile');
    }

    public function update(Request $request, $id)
    {
        $note = $this->noteRepository->find($id);
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var Note $note
             */
            $note = $form->getData();
            $note->setUser($this->getUser());
            $this->entityManager->persist($note);
            $this->entityManager->flush();
            return $this->redirectToRoute('profile');
        }
        return $this->render('notes/update.html.twig',
            ['form' => $form->createView()]
        );
    }

    public function updateStatus(int $id)
    {
        $note = $this->noteRepository->find($id);
        $note->setIsCompleted(!$note->isCompleted());
        $this->entityManager->persist($note);
        $this->entityManager->flush();
        return $this->redirectToRoute('profile');
    }
}
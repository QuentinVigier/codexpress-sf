<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/notes')] // Suffixe pour les routes du controller
class NoteController extends AbstractController
{
    #[Route('/', name: 'app_note_all', methods: ['GET'])]
    public function all(NoteRepository $nr, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $nr->findBy(['is_public' => true], ['created_at' => 'DESC']), /*Données à paginer*/
            $request->query->getInt('page', 1), /*Page courante*/
            12 /*Nombres de notes par page*/
        );
        return $this->render('note/all.html.twig', [
            'allNotes' => $pagination,
        ]);
    }

    #[Route('/n/{slug}', name: 'app_note_show', methods: ['GET'])]
    public function show(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug);
        // TODO: Mettre en place le filtre pour les notes privées
        return $this->render('note/show.html.twig', [
            'note' => $nr->findOneBySlug($slug),
            'creatorNotes' => $nr->findByCreator($note->getCreator()->getId())
        ]);
    }

    #[Route('/u/{username}', name: 'app_note_user', methods: ['GET'])]
    public function userNotes(
        string $username,
        UserRepository $user, // Cette fois on utilise le repository User
    ): Response {
        $creator = $user->findOneByUsername($username); // Recherche de l'utilisateur
        return $this->render('note/user.html.twig', [
            'creator' => $creator, // Envoie les données de l'utilisateur à la vue Twig
            'userNotes' => $creator->getNotes(), // Récupère les notes de l'utilisateur
        ]);
    }


    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'You need to be logged in to create a note.');
            return $this->redirectToRoute('app_login');
        }

        $note = new Note();
        $user = $this->getUser();  
        $form = $this->createForm(NoteType::class, $note, [
            'current_user' => $user,  // Pass the current user to the form
        ]);
        $form = $form->handleRequest($request); // Recuperation des données de la requête POST

        // Traitement des données
        if ($form->isSubmitted() && $form->isValid()) {
            $note = new Note();
            $note
                ->setTitle($form->get('title')->getData())
                ->setSlug($slugger->slug($note->getTitle()))
                ->setContent($form->get('content')->getData())
                ->setPublic($form->get('is_public')->getData())
                ->setCategory($form->get('category')->getData())
                ->setCreator($form->get('creator')->getData())
            ;
            $em->persist($note);
            $em->flush();
            
            $this->addFlash('success', 'Your note has been created');
            return $this->redirectToRoute('app_note_show', ['slug' => $note->getSlug()]);
        }
        return $this->render('note/new.html.twig', [
            'noteForm' => $form,
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/edit/{slug}', name: 'app_note_edit', methods: ['GET', 'POST'])]
    public function edit(string $slug, NoteRepository $nr, Request $request, EntityManagerInterface $em): Response
    {
        $note = $nr->findOneBySlug($slug); // Recherche de la note à modifier

        if($note->getCreator() !== $this->getUser()) {
            $this->addFlash('error', 'You are not allowed to edit this note.');
            return $this->redirectToRoute('app_note_show', ['slug' => $slug]);
        }

        $form = $this->createForm(NoteType::class, $note); // Chargement du formulaire avec les données de la note
        $form = $form->handleRequest($request); // Recuperation des données de la requête POST

        // Traitement des données
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($note);
            $em->flush();
            
            $this->addFlash('success', 'Your note has been updated');
            return $this->redirectToRoute('app_note_show', ['slug' => $note->getSlug()]);
        }
        return $this->render('note/edit.html.twig', [
            'creator' => $note->getCreator(),
            'noteForm' => $form
        ]);
    }
    
    
    #[Route('/delete/{slug}', name: 'app_note_delete', methods: ['POST'])]
    public function delete(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug); // Recherche de la note à supprimer
        // TODO: Traitement de la suppression
        $this->addFlash('success', 'Your code snippet has been deleted.');
        return $this->redirectToRoute('app_note_user');
    }
}

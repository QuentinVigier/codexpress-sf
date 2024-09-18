<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, NoteRepository $nr, PaginatorInterface $paginator): Response
    {
        $searchQuery = $request->get('q');
        
        if(!$searchQuery) {
            return $this->render('search/results.html.twig');
        }


        $pagination = $paginator->paginate(
            $nr->findByQuery($searchQuery),
            $request->query->getInt('page', 1), /*Page courante*/
            12 /*Nombres de notes par page*/
        );
        return $this->render('search/results.html.twig', [
            'allNotes' => $pagination,
            'searchQuery' => $searchQuery
        ]);
    }
}

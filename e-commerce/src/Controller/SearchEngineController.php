<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchEngineController extends AbstractController
{
    #[Route('/search/engine', name: 'app_search_engine', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository, PaginatorInterface $paginator): Response
    {
        $results = [];

        if ($request->isMethod('GET')) {
            $data = $request->query->all();
            $word = $data['word'] ?? null;

            if (!$word) {
                $this->addFlash('warning', 'Veuillez entrer un mot clé pour effectuer une recherche.');
                return $this->redirectToRoute('app_home'); // Ajustez la route selon vos besoins
            }

            $query = $productRepository->searchEngine($word); // Récupère les résultats
            $results = $paginator->paginate(
                $query, // Passez ici la requête ou les résultats
                $request->query->getInt('page', 1),
                12
            );
        }

        return $this->render('search_engine/index.html.twig', [
            'products' => $results,
            'word' => $word,
        ]);
    }
}

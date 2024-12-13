<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository) {}

    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(SessionInterface $session, Cart $cart): Response
    {
        $data= $cart->getCart($session);

        // Transmet les données à la vue
        return $this->render('cart/index.html.twig', [
            'items' => $data['cart'],  // Envoie les produits avec leurs quantités
            'total' => $data['total'],         // Envoie le total calculé
        ]);
    }

    #[Route('/cart/add/{id}/', name: 'app_cart_new', methods: ['GET'])]
    public function addToCart(int $id, SessionInterface $session): Response
    {
        // Récupère le panier depuis la session
        $cart = $session->get('cart', []);
        
        // Si le produit est déjà dans le panier, on augmente la quantité
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            // Sinon, on l'ajoute avec une quantité de 1
            $cart[$id] = 1;
        }

        // Met à jour le panier dans la session
        $session->set('cart', $cart);

        // Redirige vers la page du panier
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}/', name: 'app_cart_product_remove', methods: ['GET'])]
    public function removeToCart($id, SessionInterface $session): Response
    {
        $cart= $session->get('cart', []);
        if(!empty($cart[$id])){
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/', name: 'app_cart_remove', methods: ['GET'])]
    public function remove(SessionInterface $session): Response
    {
        $session->set('cart', []);

        return $this->redirectToRoute('app_cart');
    }
}

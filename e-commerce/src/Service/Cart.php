<?php

namespace App\Service;

use App\Repository\ProductRepository;

class Cart
{

    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    public function getCart($session): array
    {
        // Récupère le panier depuis la session
        $cart = $session->get('cart', []);
        $cartWithData = [];

        // Récupère les informations des produits et la quantité
        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity,
            ];
        }

        // Calcul du total
        $total = array_sum(array_map(function ($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWithData));

        return [
            'cart' => $cartWithData,
            'total' => $total,
        ];
    }
}

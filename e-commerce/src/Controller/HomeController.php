<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findBy([], ['id'=>"DESC"]),
        ]);
    }

    #[Route('home/product/{id}/show', name: 'app_home_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {


        return $this->render('home/show.html.twig', [
            'products' => $product,
        ]);
    }
}

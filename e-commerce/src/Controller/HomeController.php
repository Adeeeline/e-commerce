<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Form\CommentType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $search = $productRepository->searchEngine('est');

        $data = $productRepository->findBy([], ['id' => "DESC"]);
        $products = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            12,
        );

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('home/product/{id}/show', name: 'app_home_product_show', methods: ['GET', 'POST'])]
public function show(Product $product, ProductRepository $productRepository, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, Request $request): Response
{
    $lastProduct = $productRepository->findBy([], ['id' => 'DESC'], 4);

    $comment = new Comment();
    $form = $this->createForm(CommentType::class, $comment);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $comment->setCreatedAt(new \DateTimeImmutable());
        $comment->setProduct($product);
        $comment->setUser($this->getUser());
        $comment->setRating($form->get('rating')->getData());

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_home_product_show', ['id' => $product->getId()]);
    }

    return $this->render('home/show.html.twig', [
        'form' => $form->createView(),
        'product' => $product,
        'products' => $lastProduct,
        'categories' => $categoryRepository->findAll(),
        'comments' => $product->getComments(),
    ]);
}

    #[Route('home/product/subcategory/{id}/filter', name: 'app_home_product_filter', methods: ['GET'])]
    public function filter($id, SubCategoryRepository $subCategoryRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request, ProductRepository $productRepository): Response
    {
        $products = $subCategoryRepository->find($id)->getProducts();
        $subCategory = $subCategoryRepository->find($id);
        $data = $productRepository->findBy([], ['id' => "DESC"]);
        $products = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            12,
        );

        return $this->render('home/filter.html.twig', [
            'products' => $products,
            'categories' => $categoryRepository->findAll(),
            'subCategory' => $subCategory,
        ]);
    }
}

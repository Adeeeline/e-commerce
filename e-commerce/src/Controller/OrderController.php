<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Service\Cart;
use App\Form\OrderType;
use App\Entity\OrderProducts;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
public function index(
    Request $request,
    SessionInterface $session,
    ProductRepository $productRepository,
    Cart $cart,
    EntityManagerInterface $entityManager
): Response {
    // Vérifier si l'utilisateur est connecté
    if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
        return $this->redirectToRoute('app_login');
    }

    // Récupérer les données du panier
    $data = $cart->getCart($session);

    // Créer une nouvelle commande
    $order = new Order();
    $form = $this->createForm(OrderType::class, $order);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $this->getUser();
        
        // Incrémenter le compteur d'achats de l'utilisateur
        if ($user instanceof User) { // Vérification explicite pour éviter toute erreur
            $user->incrementPurchaseCount();
            $entityManager->persist($user);
        }

        // Traitement de la commande
        if ($order->isPayOnDelivery() && !empty($data['total'])) {
            $order->setTotalPrice($data['total']);
            $order->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($order);

            foreach ($data['cart'] as $value) {
                $orderProduct = new OrderProducts();
                $orderProduct->setOrder($order);
                $orderProduct->setProduct($value['product']);
                $orderProduct->setQuantity($value['quantity']);
                $entityManager->persist($orderProduct);
            }

            $entityManager->flush();
            $session->set('cart', []); // Vider le panier après la commande
            return $this->redirectToRoute('order_ok_message');
        }
    }

    return $this->render('order/index.html.twig', [
        'form' => $form->createView(),
        'total' => $data['total'], // Utilisez les données calculées du panier
    ]);
}


    #[Route('/admin/orders', name: 'app_orders_show')]
    public function getAllOrder(PaginatorInterface $paginator, Request $request, OrderRepository $orderRepository): Response
    {
        $data = $orderRepository->findBy([], ['id' => 'DESC']);
        $order = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            6,
        );
        return $this->render('order/orders.html.twig', [
            "orders" => $order,
        ]);
    }

    #[Route('/admin/order/{id}/is-completed/update', name: 'app_order_is_completed_update')]
    public function isCompletedUpdate($id, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        $order = $orderRepository->find($id);
        $order->setIsCompleted(true);
        $entityManager->flush();
        $this->addFlash('succes', 'Modification effectuée');
        return $this->redirectToRoute('app_orders_show');
    }

    #[Route('/admin/order/{id}/remove', name: 'app_order_remove')]
    public function removeOrder(Order $order, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($order);
        $entityManager->flush();
        $this->addFlash('succes', 'La commande a été supprimé');
        return $this->redirectToRoute('app_orders_show');
    }

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function cityShippingCost(City $city): Response
    {
        $cityShippingPrice = $city->getShippingCost();

        return new Response(json_encode(['status' => 200, "message" => 'on', 'content' => $cityShippingPrice]));
    }

    #[Route('/order-ok-message', name: 'order_ok_message')]
    public function orderMessage(): Response
    {
        return $this->render('order/order_message.html.twig');
    }
}

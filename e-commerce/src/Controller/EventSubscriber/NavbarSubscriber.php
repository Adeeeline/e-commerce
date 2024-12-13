<?php

namespace App\Controller\EventSubscriber;

use App\Repository\CategoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class NavbarSubscriber implements EventSubscriberInterface
{
    private $categoryRepository;
    private $twig;

    public function __construct(CategoryRepository $categoryRepository, Environment $twig)
    {
        $this->categoryRepository = $categoryRepository;
        $this->twig = $twig;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        // Inject the categories into all templates
        $categories = $this->categoryRepository->findAll();
        $this->twig->addGlobal('categories', $categories);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}

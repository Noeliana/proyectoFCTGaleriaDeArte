<?php

namespace App\EventSubscriber;

use App\Repository\TagRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class GlobalTwigSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $tagRepository;

    public function __construct(Environment $twig, TagRepository $tagRepository)
    {
        $this->twig = $twig;
        $this->tagRepository = $tagRepository;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $this->twig->addGlobal('tags', $this->tagRepository->findAll());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onKernelController',
        ];
    }
}
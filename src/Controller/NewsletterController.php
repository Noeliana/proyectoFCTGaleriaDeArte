<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NewsletterController extends AbstractController
{
    #[Route('/newsletter/subscribe', name: 'app_newsletter_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, MailerInterface $mailer): Response
    {
        $emailInput = $request->request->get('email');

        if (!filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Correo inválido.');
            return $this->redirectToRoute('app_home');
        }

        // Enviar correo al administrador
        $email = (new Email())
            ->from('no-reply@tusitio.com')
            ->to('noeliasa169@gmail.com')
            ->subject('Nueva suscripción')
            ->text("Nuevo suscriptor: $emailInput");

        $mailer->send($email);

        $this->addFlash('success', '¡Gracias por suscribirte!');
        return $this->redirectToRoute('app_home');
    }
}
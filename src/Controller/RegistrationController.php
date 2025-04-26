<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hashear la contraseña primero
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Obtener el email
            $email = $user->getEmail();

            // Crear el username base
            $baseUsername = explode('@', $email)[0];
            $finalUsername = $baseUsername;
            $counter = 1;

            // Buscar si el username ya existe
            while ($entityManager->getRepository(User::class)->findOneBy(['username' => $finalUsername])) {
                $finalUsername = $baseUsername . $counter;
                $counter++;
            }

            // Establecer el username único
            $user->setUsername($finalUsername);

            // Guardar el usuario
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', '¡Registro exitoso! Tu nombre mágico es: ' . $finalUsername . ' ✨');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }


}


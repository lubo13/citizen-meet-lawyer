<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Security\CustomLoginFormAuthenticator;
use App\Utils\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/{_locale}/register", name="app_register", requirements={"_locale": "%app.supported_locales%"})
     */
    public function register(
        Request $request,
        User $userUtils,
        GuardAuthenticatorHandler $guardHandler,
        CustomLoginFormAuthenticator $authenticator
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('easyadmin');
        }

        $form    = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userUtils->createUser($form);

            $userUtils->sendSuccessfulRegisterationMail($user);

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main'
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

}

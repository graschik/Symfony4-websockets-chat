<?php

namespace App\Controller;


use App\Form\LoginType;
use App\Form\SignupType;
use App\Service\SignupFormHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/signup", name="signup")
     * @param Request $request
     * @param SignupFormHandler $signupFormHandler
     * @return Response
     */
    public function signup(
        Request $request,
        SignupFormHandler $signupFormHandler
    ): Response
    {
        $form = $this->createForm(SignupType::class);

        if ($signupFormHandler->handle($form, $request)) {
            return $this->redirectToRoute('home');
        }
        return $this->render('security/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login",name="login")
     *
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(
        Request $request,
        AuthenticationUtils $authenticationUtils
    ): Response
    {
        $form = $this->createForm(LoginType::class);

        $authenticationError = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'authenticationError' => $authenticationError,
            'lastUsername' => $lastUsername
        ]);
    }
}
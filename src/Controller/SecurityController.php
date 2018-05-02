<?php

namespace App\Controller;


use App\Form\SignupType;
use App\Service\SignupFormHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

        return $this->render('security/signup.html.twig',[
            'form'=>$form->createView(),
        ]);
    }
}
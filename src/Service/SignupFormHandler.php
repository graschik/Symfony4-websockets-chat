<?php

namespace App\Service;


use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SignupFormHandler
{
    private $signupService;

    public function __construct(SignupService $signupService)
    {
        $this->signupService = $signupService;
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @return bool
     */
    public function handle(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->signupService->signup($user);

            return true;
        }

        return false;
    }
}
<?php

namespace App\Controller;


use App\Service\Ajax\AjaxQuestionsSearch;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends Controller
{
    private $ajaxQuestionsSearch;

    /**
     * AjaxController constructor.
     */
    public function __construct()
    {
    }

    /**
     * @Route("/ajax/front-controller",name="ajax.front-controller")
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function ajaxAction(Request $request): RedirectResponse
    {
        return $this->redirectToRoute($request->get('route'), [
            'value' => $request->get('value')
        ]);
    }

    /**
     * @Route("/ajax/questions-search/{value}", name="ajax.questions-search")
     *
     * @param string $value
     * @return JsonResponse
     */
    public function ajaxQuestionSearch(string $value): JsonResponse
    {
        $result = $this
            ->ajaxQuestionsSearch
            ->searchQuestions($value);

        return new JsonResponse($result);
    }
}
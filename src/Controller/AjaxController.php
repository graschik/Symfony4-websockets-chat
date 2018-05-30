<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends Controller
{
    private $messageService;

    /**
     * AjaxController constructor.
     * @param MessageService $messageService
     */
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * @Route("/ajax/front-controller",name="ajax.front-controller")
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function ajaxAction(Request $request): RedirectResponse
    {
        return $this->redirectToRoute($request->get('route'));
    }

    /**
     * @Route("/ajax/message-search", name="ajax.message-search")
     *
     * @param MessageService $messageService
     * @return JsonResponse
     */
    public function ajaxQuestionSearch(MessageService $messageService): JsonResponse
    {
        $session = new Session();
        $messageCount = $session->get('current_message_count');
        $session->set('current_message_count', $messageCount - $messageService::MESSAGE_LIMIT);

        return new JsonResponse($messageService->prepareMessagesForSending($messageService->getPreviousMessages($messageCount)));
    }
}
<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\MessageService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/chat", name="home")
     * @param Request $request
     * @param UserService $userService
     * @param MessageService $messageService
     * @return Response
     */
    public function homeAction(
        Request $request,
        UserService $userService,
        MessageService $messageService
    )
    {
        $session = new Session();
        $session->set('current_user_id', $this->getUser()->getId());
        $session->set('current_message_count', $messageService->getMessageCount() - $messageService::MESSAGE_LIMIT);

        return $this->render('chat/chat.html.twig', [
            'user' => $userService->getUserById($this->getUser()->getId()),
            'messages' => $messageService->prepareMessagesForSending($messageService->getLastMessages()),
        ]);
    }
}
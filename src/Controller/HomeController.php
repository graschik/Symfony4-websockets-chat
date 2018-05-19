<?php

namespace App\Controller;

use App\Entity\User;
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
     * @Route("/", name="home")
     * @param Request $request
     * @return Response
     */
    public function homeAction(Request $request)
    {
        //phpinfo();
        $session = new Session();
        if ($this->getUser())
            $session->set('current_user_id', $this->getUser()->getId());

        $users = $this->getDoctrine()->getManager()->getRepository(User::class)->getActive();

        return $this->render('chat/chat.html.twig', [
            'users' => $users,
        ]);
    }
}
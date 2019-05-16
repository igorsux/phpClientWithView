<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
    use ApiTrait;

    /**
     * @Route("/login", name="login-action")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        if($request->getMethod() == "POST") {

            $api = $this->getApi()->setByToken(true);

            $login = $request->get('login');
            $password = $request->get('password');

            $answer = $api->authAction($login, $password);

            $api->setBearer($answer['bearer']);

            file_put_contents('token.txt', $answer['bearer']);

            return $this->redirectToRoute('index');
        }

        $data = [
          'action' => $this->generateUrl("login-action")
        ];

        return $this->render('login/login.html.twig', $data);
    }
}
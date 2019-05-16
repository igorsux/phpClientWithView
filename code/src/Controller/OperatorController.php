<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OperatorController extends AbstractController
{
    use ApiTrait;

    /**
     * @Route("/operators", name="operator-list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $requestData = [];

        $response = $this->getApi()->getOperatorList($requestData);

        dump($response);

        $data = [
            'items' => $response['items'],
        ];

        dd('Operators');

        return $this->render('sender-names/templates/index.html.twig', $data);
    }
}
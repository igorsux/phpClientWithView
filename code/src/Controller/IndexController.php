<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    use ApiTrait;

    /**
     * @Route("/", name="index")
     */
    public function getNotification()
    {
        $api = $this->getApi();

        $answer = $api->clientInfo();
        $dictionaryInfo = $api->dictionaryInfo();

        $data = [
            'info' => $answer['info'],
            'dictionaryInfo' => $dictionaryInfo
        ];

        return $this->render('index.html.twig', $data);
    }
}
<?php

namespace App\Controller\lkApi;

use Throwable;
use App\Redsms\LkRedsmsApi;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    /**
     * @Route("/lk/balance", name="lk-balance")
     */
    public function indexAction()
    {
        try {

            $balance = $this->getApi(true)->anyGetMethod('balance.php');
            $bases = $this->getApi()->anyGetMethod('base.php');

            dump($balance);
            dump($bases);

        } catch (Throwable $e) {
            dump($e);
        }
        dd();
    }

    /**
     * @Route("/lk/sendTest", name="lk-send")
     * @param Request $request
     * @return Response
     */
    public function sendAction(Request $request)
    {
        $data = [
            'phone' => "79296643352",
            'text' => "Привет",
            'sender' => 'REDSMS.RU',
        ];

        try {
            $response = $this->getApi()->anyGetMethod('send.php', $data);

            dump($response);
        } catch (Throwable $e) {
            dump($e);
        }
        dd(1);

    }

    private function getApi($new = false)
    {
        $login = 'shatilov';
        $apiKey = '';
        $apiUrl = 'https://lk.redsms.ru/get';

        if ($new) {
            $apiUrl = 'https://dev.redsms.ru/old-api/get';
            $login = 'igorsux';
            $apiKey = '';
        }

        return new LkRedsmsApi($login, $apiKey, $apiUrl);
    }
}
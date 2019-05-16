<?php

namespace App\Controller;

use App\Redsms\RedsmsApiSimple;

trait ApiTrait
{
    static function getDayFromToken($token)
    {
        list($token, $sign) = explode('.', $token);
        $tokenArray = json_decode(base64_decode($token), true);
        $day = round(($tokenArray['iat'] - time()) / 60 / 60 / 24, 1);

        return $day;
    }

    /**
     * @param bool $auth
     * @return RedsmsApiSimple
     */
    protected function getApi($auth = true)
    {
        $config = [
            'login' => 'igorsux',
            'apiKey' => 'a4c4347545a9196903d5b181372e6077237e8d5a',
            'apiUrl' => 'https://dev.redsms.ru/api',
            'phone' => '+79296643352',
            'smsSenderName' => 'PaidTest',
            'viberSenderName' => 'ViberDev',
        ];

        $api = new RedsmsApiSimple($config['login'], $config['apiKey'], $config['apiUrl']);

        if ($auth && $bearer = file_get_contents('token.txt')) {
            $api->setBearer($bearer)->setByToken(true);
        }

        return $api;
    }

    private function getOperators()
    {
        foreach ($this->getApi()->anyGetMethod('operator', [])['items'] as $operator) {
            $operators[$operator['id']] = $operator['name'];
        }

        $operators[0] = 'Остальные';

        return $operators;
    }

    private function getCountriesFilter()
    {
        return [
            176 => 'Россия',
            172 => 'Что то там',
        ];
    }

    private function getStatuses()
    {
        return [
            "moderation" => "moderation",
            "reject" => "reject",
            "created" => "created",
            "progress" => "progress",
            "delivered" => "delivered",
            "timeout" => "timeout",
            "undelivered" => "undelivered",
            "error" => "error",
            "no_money" => "no_money",
            "read" => "read",
            "reply" => "reply",
            "stop_list" => "stop_list",
            "doubled" => "doubled",
            "bad_number" => "bad_number",
            "bad_name" => "bad_name",
        ];
    }
}
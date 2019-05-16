<?php

namespace App\Redsms;

use Exception;

class LkRedsmsApi
{
    private $login;
    private $apiKey;

    private $apiUrl = 'https://lk.redsms.ru/get';

    public function __construct($login, $apiKey, $apiUrl = null)
    {
        $this->login = $login;
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl ?? $this->apiUrl;
    }


    /**
     * @param $methodUrl
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function anyGetMethod($methodUrl, $data = [])
    {
        $data['login'] = $this->login;
        $data['timestamp'] = time()-3600*3;
        ksort($data);
        reset($data);
        $data['signature'] = md5(implode('', $data) . $this->apiKey);

        return $this->sendGet($methodUrl, $data);
    }

    /**
     * @param $url
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    protected function sendGet($url, $data = [])
    {
        dump($this->apiUrl);
        $curlResource = curl_init();
        $vars = http_build_query($data, '', '&');

        curl_setopt($curlResource, CURLOPT_URL, $this->apiUrl."/".$url."?$vars");
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, 1);

        return $this->getCurlResult($curlResource);
    }

    /**
     * @param $curlResource
     * @return mixed
     * @throws Exception
     */
    protected function getCurlResult($curlResource)
    {
        $response = curl_exec($curlResource);
        $info = curl_getinfo($curlResource);

        curl_close($curlResource);
        $responseArray = json_decode($response, true);

        if ($info['http_code'] != 200) {
            dump($responseArray);
            throw new Exception($responseArray['error_message'], $info['http_code']);
        }

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new Exception('Error response format', $info['http_code']);
        }

        return $responseArray;
    }
}
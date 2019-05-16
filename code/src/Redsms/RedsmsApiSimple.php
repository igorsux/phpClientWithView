<?php

namespace App\Redsms;

class RedsmsApiSimple
{
    const SMS_TYPE = 'sms';

    const VIBER_TYPE = 'viber';

    const RESEND_TYPE = 'viber,sms';

    protected $login;
    protected $apiKey;

    protected $byToken = false;

    protected $bearer = null;

    private $apiUrl = 'https://cp.redsms.ru/api';

    public function __construct($login, $apiKey, $apiUrl = null)
    {
        $this->login = $login;
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl ? $apiUrl : $this->apiUrl;
    }

    public function anyGetMethod($methodUrl, $data = [])
    {
        return $this->sendGet($methodUrl, $data);
    }

    public function anyPostMethod($methodUrl, $data = [])
    {
        return $this->sendPost($methodUrl, $data);
    }
    public function anyDeleteMethod($methodUrl)
    {
        return $this->sendDelete($methodUrl);
    }

    public function clientInfo()
    {
        $methodUrl = 'client/info';

        return $this->sendGet($methodUrl);
    }

    public function phoneInfo($data)
    {
        $methodUrl = 'client/phone-info';

        return $this->sendGet($methodUrl, $data);
    }

    public function dictionaryInfo()
    {
        $methodUrl = 'directory';

        return $this->sendGet($methodUrl);
    }

    public function authAction($login, $password)
    {
        $methodUrl = 'auth';

        $data = [
            'login' => $login,
            'password' => $password
        ];

        return $this->sendPost($methodUrl, $data);
    }

    public function getNotificationAccounts()
    {
        $methodUrl = 'settings/notifications';

        return $this->sendGet($methodUrl);
    }

    public function deleteNotifyAccount($id)
    {
        $methodUrl = 'settings/notifications/' . $id;

        return $this->sendDelete($methodUrl);
    }

    public function deleteSenderNameSmsTemplate($id)
    {
        $methodUrl = 'sender-name/templates/sms/' . $id;

        return $this->sendDelete($methodUrl);
    }

    public function getSenderNameSmsTemplate($id, $data = [])
    {
        $methodUrl = 'sender-name/templates/sms/' . $id;

        return $this->sendGet($methodUrl, $data);
    }

    public function createSenderNameSmsTemplate($data)
    {
        $methodUrl = 'sender-name/templates/sms';

        return $this->sendPost($methodUrl, $data);
    }

    public function updateSenderNameSmsTemplate($id, $data)
    {
        $methodUrl = 'sender-name/templates/sms/'.$id;

        return $this->sendPost($methodUrl, $data);
    }

    public function getSenderNameSmsTemplateList($data = [])
    {
        $methodUrl = 'sender-name/templates/sms';

        return $this->sendGet($methodUrl, $data);
    }

    public function getOperatorList($data = [])
    {
        $methodUrl = 'operator';

        return $this->sendGet($methodUrl, $data);
    }

    public function getNotificationAccount($id, $data = [])
    {
        $methodUrl = 'settings/notifications/' . $id;

        return $this->sendGet($methodUrl, $data);
    }

    public function editNotificationAccount($id, $data = [])
    {
        $methodUrl = 'settings/notifications/' . $id;

        return $this->sendPost($methodUrl, $data);
    }

    public function createNotifyAccount($data)
    {
        $methodUrl = 'settings/notifications';

        return $this->sendPost($methodUrl, $data);
    }

    public function deleteFile($idFile)
    {
        $methodUrl = 'storage/' . $idFile;

        return $this->sendDelete($methodUrl);
    }

    public function fileInfo()
    {
        $methodUrl = 'storage';

        return $this->sendGet($methodUrl);
    }

    public function getContactBases($data = [])
    {
        $methodUrl = 'contact-base';

        return $this->sendGet($methodUrl, $data);
    }

    public function getContactBase($baseId, $data = [])
    {
        $methodUrl = 'contact-base/' . $baseId;

        return $this->sendGet($methodUrl, $data);
    }

    public function getContact($baseId, $contactId, $data = [])
    {
        $methodUrl = "contact-base/$baseId/contact/$contactId";

        return $this->sendGet($methodUrl, $data);
    }

    public function getContactByBase($id, $data)
    {
        $methodUrl = 'contact-base/' . $id . '/contact/';

        return $this->sendGet($methodUrl, $data);
    }
    public function downloadBase($id, $data)
    {
        $methodUrl = 'contact-base/' . $id . '/download';

        return $this->sendGet2($methodUrl, $data);
    }

    public function postContactBase($id, $data)
    {
        $methodUrl = 'contact-base/' . $id;

        return $this->sendPost($methodUrl, $data);
    }

    public function getDirectory()
    {
        $methodUrl = 'directory';

        return $this->sendGet($methodUrl);
    }

    public function uploadFile($fileNAME)
    {
        $methodUrl = 'storage';

        return $this->postFile($methodUrl, $fileNAME);
    }

    public function createBase($data)
    {
        $methodUrl = 'contact-base';

        return $this->sendPost($methodUrl, $data);
    }

    public function createDispatch($data)
    {
        $methodUrl = 'dispatch';

        return $this->sendPost($methodUrl, $data);
    }

    public function sendSMS($to, $text, $from, $route = RedsmsApiSimple::SMS_TYPE)
    {
        $methodUrl = 'message';
        $to = is_array($to) ? $to : [$to];

        $data = [
            'to' => implode(',', $to),
            'text' => $text,
            'from' => $from,
            'route' => $route,
        ];

        return $this->sendPost($methodUrl, $data);
    }

    public function sendViber($to, $text, $from, $btnText, $btnUrl, $imageUrl)
    {
        $methodUrl = 'message';

        $to = is_array($to) ? $to : [$to];
        $data = [
            'to' => implode(',', $to),
            'text' => $text,
            'from' => $from,
            'route' => RedsmsApiSimple::VIBER_TYPE,
            'viber.btnText' => $btnText,
            'viber.btnUrl' => $btnUrl,
            'viber.imageUrl' => $imageUrl,
        ];

        return $this->sendPost($methodUrl, $data);
    }

    public function sendMessage($data)
    {
        $methodUrl = 'message';

        return $this->sendPost($methodUrl, $data);
    }

    public function messageInfo($uuid)
    {
        $methodUrl = 'message/' . $uuid;

        return $this->sendGet($methodUrl);
    }

    public function senderNameList($data = [])
    {
        $methodUrl = 'sender-name';

        return $this->sendGet($methodUrl, $data);
    }

    public function getSenderName($id, $data = [])
    {
        $methodUrl = 'sender-name/' . $id;

        return $this->sendGet($methodUrl, $data);
    }

    protected function sendGet2($url, $data = [])
    {
        $curlResource = curl_init();
        $vars = http_build_query($data, '', '&');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="file.csv"');
        curl_setopt($curlResource, CURLOPT_URL, $this->apiUrl."/".$url."?$vars");
        curl_setopt($curlResource, CURLOPT_HTTPHEADER, $this->getHeaders($data));
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curlResource, CURLOPT_WRITEFUNCTION, function($curl, $data) {
            echo $data;
            return strlen($data);
        });
        curl_exec($curlResource);
        curl_close($curlResource);
    }

    protected function sendGet($url, $data = [])
    {
        $curlResource = curl_init();
        $vars = http_build_query($data, '', '&');
        curl_setopt($curlResource, CURLOPT_URL, $this->apiUrl."/".$url."?$vars");
        curl_setopt($curlResource, CURLOPT_HTTPHEADER, $this->getHeaders($data));
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, 1);

        return $this->getCurlResult($curlResource);
    }

    protected function sendPost($url, $data = [])
    {
        $headers = $this->getHeaders($data);
        $headers[] ='Content-Type: application/json';
        $curlResource = curl_init();
        curl_setopt($curlResource, CURLOPT_URL, $this->apiUrl . "/" . $url);
        curl_setopt($curlResource, CURLOPT_POST, 1);
        curl_setopt($curlResource, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curlResource, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, 1);

        return $this->getCurlResult($curlResource);
    }

    protected function sendDelete($url)
    {
        $curlResource = curl_init();
        curl_setopt($curlResource, CURLOPT_URL, $this->apiUrl . "/" . $url);
        curl_setopt($curlResource, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($curlResource, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, true);

        return $this->getCurlResult($curlResource);
    }

    protected function postFile($url, $name)
    {
        $curlResource = curl_init();
        curl_setopt($curlResource, CURLOPT_URL, $this->apiUrl . "/" . $url);
        curl_setopt($curlResource, CURLOPT_POST, true);
        curl_setopt($curlResource, CURLOPT_POSTFIELDS, ['file' => new \CURLFile($name)]);
        curl_setopt($curlResource, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, true);

        return $this->getCurlResult($curlResource);
    }

    protected function getCurlResult($curlResource)
    {
        $response = curl_exec($curlResource);
        $info = curl_getinfo($curlResource);
        curl_close($curlResource);
        $responseArray = json_decode($response, true);

        if ($info['http_code'] != 200) {
            dump($responseArray);
            throw new \Exception($responseArray['error_message'], $info['http_code']);
        }

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception('Error response format', $info['http_code']);
        }


        return $responseArray;
    }

    protected function getHeaders($data = [])
    {
        if ($this->byToken){
            if($this->bearer)
            {
                return [
                    'bearer: '. $this->bearer
                ];

            } else {
                return [];
            }
        }
        ksort($data);
        reset($data);
        $ts = microtime() . rand(0, 10000);

        return [
            'login: ' . $this->login,
            'ts: ' . $ts,
            'sig: ' . md5(implode('', $data) . $ts . $this->apiKey),
        ];
    }

    /**
     * @param bool $byToken
     * @return RedsmsApiSimple
     */
    public function setByToken(bool $byToken): RedsmsApiSimple
    {
        $this->byToken = $byToken;

        return $this;
    }

    /**
     * @param $bearer
     * @return RedsmsApiSimple
     */
    public function setBearer($bearer)
    {
        $this->bearer = $bearer;

        return $this;
    }
}
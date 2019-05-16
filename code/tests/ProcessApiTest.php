<?php

namespace App\Tests;

use App\Controller\ApiTrait;
use App\Redsms\RedsmsApiSimple;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProcessApiTest extends WebTestCase
{
    use ApiTrait;

    private $redSms = null;

    private function redSms(): RedsmsApiSimple
    {
        if (!$this->redSms) {
            $login = "login";
            $password = "password";

            $this->redSms = $this->getApi(false)
                ->setBearer($this->getApi(false)->authAction($login, $password)['bearer'])
                ->setByToken(true);
        }

        return $this->redSms;
    }

    public function testCompare()
    {
        $types = ['sms', 'vk', 'viber'];

        $cases['sms'] = ['message' => '123', 'template' => '123'];
        $cases['vk'] = ['message' => '123', 'template' => '123'];
        $cases['viber'] = ['message' => '123', 'template' => '123'];

        foreach ($types as $type) {
            $requestData = $cases[$type];
            $response = $this->redSms()->anyGetMethod('sender-name/templates/' . $type . '/compare', $requestData);
            $this->assertTrue($response['result']);
        }

        $cases['sms'] = ['template' => 'Иван %d', 'message' => 'Иван 123'];
        $cases['vk'] = ['template' => 'Иван #code#', 'message' => 'Иван 123'];
        $cases['viber'] = ['template' => 'Иван \d+', 'message' => 'Иван Иван'];

        foreach ($types as $type) {
            $requestData = $cases[$type];
            $response = $this->redSms()->anyGetMethod('sender-name/templates/' . $type . '/compare', $requestData);
            $this->assertTrue($response['result']);
        }
    }


        public function testDeleteBySearchCase()
        {
            $this->clearBases();

            $baseId = $this->createBaseWithContacts(1, "testBase1");

            $contacts = $this->redSms()->anyGetMethod($this->getBaseContactsUrl($baseId));

            $this->assertTrue($contacts['success']);
            $this->assertTrue($contacts['total'] == 10);

            $processData = [
                'search' => "664",
                'type' => 'delete',
                'contactBaseId' => $baseId,
            ];

            $baseProcess = $this->redSms()->anyPostMethod('contact-base-process', $processData);
            $this->assertTrue($baseProcess['success']);

            $this->waitContactBaseProcess($baseProcess['id']);

            $contacts = $this->redSms()->anyGetMethod($this->getBaseContactsUrl($baseId));
            $this->assertTrue($contacts['success']);
            $this->assertTrue($contacts['total'] == 0);

            $this->clearBases();
        }

        public function testMoveBySearchCase()
        {
            $this->clearBases();

            $baseId = $this->createBaseWithContacts(1, "testBase1");
            $baseId2 = $this->createBaseWithContacts(2, "testBase2");

            $processData = [
                'search' => "664",
                'type' => 'move',
                'contactBaseId' => $baseId,
            ];

            $baseProcess = $this->redSms()->anyPostMethod('contact-base-process', $processData);
            $this->assertTrue($baseProcess['success']);

            $this->waitContactBaseProcess($baseProcess['id']);

            $base = $this->waitContactBaseStatusReady($baseId);

            $this->assertTrue($base['contactCount'] == 20);

            $contacts = $this->redSms()->anyGetMethod($this->getBaseContactsUrl($baseId2));

            $this->assertTrue($contacts['success']);
            $this->assertTrue($contacts['total'] == 0);

            $this->clearBases();
        }

        public function testCopyBySearchCase()
        {
            $this->clearBases();

            $baseId = $this->createBaseWithContacts(1, "testBase1");

            $base2 = $this->redSms()->createBase(['name' => "testBase2"]);

            $baseId2 = $base2['id'];

            $processData = [
                'search' => "664",
                'type' => 'copy',
                'contactBaseId' => $baseId2,
            ];

            $baseProcess = $this->redSms()->anyPostMethod('contact-base-process', $processData);
            $this->assertTrue($baseProcess['success']);

            $this->waitContactBaseProcess($baseProcess['id']);

            $base = $this->waitContactBaseStatusReady($baseId2);

            $this->assertTrue($base['contactCount'] == 10);

            $contacts = $this->redSms()->anyGetMethod($this->getBaseContactsUrl($baseId));

            $this->assertTrue($contacts['success']);
            $this->assertTrue($contacts['total'] == 10);

            $this->clearBases();
        }

    private function waitContactBaseProcess($contactBaseProcessId)
    {
        $tryCount = 1;
        do {
            $tryCount++;
            $baseProcess = $this->redSms()->anyGetMethod("contact-base-process/$contactBaseProcessId");
        } while ($tryCount < 10 && $baseProcess['status'] != 'complete');

        return $baseProcess;
    }

    private function waitContactBaseStatusReady($contactBaseId)
    {
        $tryCount = 1;
        do {
            $tryCount++;
            $base = $this->redSms()->anyGetMethod("contact-base/$contactBaseId", ['fields' => 'all']);
        } while ($tryCount < 10 && $base['status'] != 'ready');

        sleep(5);

        return $base;
    }

    private function clearBases()
    {
        foreach ($this->redSms()->getContactBases()['items'] as $base) {
            $this->redSms()->anyDeleteMethod('contact-base/' . $base['id']);
        }
    }

    public function getBaseContactsUrl($baseId)
    {
        return "contact-base/{$baseId}/contact/";
    }

    private function createBaseWithContacts($i, $name)
    {
        $base = $this->redSms()->createBase(['name' => $name]);
        $this->assertTrue($base['success']);

        $baseUrl = $this->getBaseContactsUrl($base['id']);

        $contactCount = 10;

        foreach (range(1000 * $i, (1000 * $i) + $contactCount - 1) as $prefix) {
            $contact = $this->redSms()->anyPostMethod($baseUrl, ['phone' => '+7929664' . $prefix, 'firstName' => "иван_" . $prefix]);
            $this->assertTrue($contact['success']);
        }

        $contacts = $this->redSms()->anyGetMethod($baseUrl);

        $this->assertTrue($contacts['success']);
        $this->assertTrue($contacts['total'] == $contactCount);

        return $base['id'];
    }
}
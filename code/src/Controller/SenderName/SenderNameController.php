<?php

namespace App\Controller\SenderName;

use App\Controller\ApiTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SenderNameController extends AbstractController
{
    use ApiTrait;

    /**
     * @Route("/senderNames", name="sender-names")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $requestData = [
            'fields' => 'all',
            'perPage' =>$request->get('perPage')  ?? 10,
            'orderBy' => $request->get('orderBy') ?? 'createdAt',
            'orderDirection' =>  $request->get('orderDirection') ?? 'desc',
            'type' => $request->get('type') ?? 'sms',
            'search' => $request->get('search') ?? '',
        ];

        $start = microtime(true);
        $response = $this->getApi()->anyGetMethod('sender-name', $requestData);
        $end = microtime(true) - $start;

        $data = [
            'timeResponse' => round($end, 3) . " ms",
            'requestData' => $requestData,
            'response' => $response,
            'items' => $response['items'],
            'createSmsAction' => $this->generateUrl("sender-name-sms-create"),
            'createViberAction' => $this->generateUrl("sender-name-viber-create"),
        ];

        return $this->render('sender-names/index.html.twig', $data);
    }

    /**
     * @Route("/senderNames/vkInfo}", name="sender-name-vk-info", methods={"GET"})
     * @param Request $request
     * @return void
     */
    public function getVkInfo(Request $request)
    {
        $token = '184633b80ee650c3be814c84658415efaa7e721f94f8648de62a17e27be523999582f5d3cc7889bcdcd92';

       $response =  $this->getApi()->anyPostMethod('sender-name/vk/group-info',['token' => $token]);
       dd($response['info']);
    }

    /**
     * @Route("/senderNames/sms/{senderNameId}", name="sender-name-sms-show", methods={"GET"}, requirements={"senderNameId":"\d+"})
     * @param $senderNameId
     * @param Request $request
     * @return void
     */
    public function showAction($senderNameId, Request $request)
    {
       $name = $this->getApi()->anyGetMethod('sender-name/'.$senderNameId, ['fields'=> 'all']);
        dd($name);
    }

    /**
     * @Route("/senderNames/sms/create", name="sender-name-sms-create")
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        dump(1);
        dump($request->request->all());
        if ($request->getMethod() == "POST") {
            $senderNameData = [
                'type' => 'sms',
                'status' => $request->get('status'),
                'name' => $request->get('name'),
                'inn' => $request->get('inn'),
                'companyName' => $request->get('companyName'),
                'operator' => [],
                'trademarkFile' => $request->get('trademarkFile') ,
                'licenseAgreement' => $request->get('licenseAgreement'),
                'companyDocument' => $request->get('companyDocument'),
                'domainCertificateFile' => $request->get('domainCertificateFile'),
                'guaranteeScanFile' => $request->get('guaranteeScanFile'),
            ];

            foreach ($request->get('operator') as $id => $data) {
                if ($data['paid'] == 'paid') {
                    $senderNameData['operator'][$id] = [
                        'paid' => 'true',
                        'period' => $data['period']
                    ];
                }
            }

            dd(json_encode(array_filter($senderNameData)));

            $this->getApi()->anyPostMethod('sender-name', array_filter($senderNameData));

            return $this->redirectToRoute("sender-names");
        }

        if ($request->getMethod() == "GET") {
            $data = [
                'action' => $this->generateUrl("sender-name-sms-create"),
                'operators' => $this->getApi()->getOperatorList()['items']
            ];

            return $this->render('sender-names/create-sms.html.twig', $data);
        }

        return $this->redirect("sender-names");
    }

    /**
     * @Route("/senderNames/viber/create", name="sender-name-viber-create")
     * @param Request $request
     * @return Response
     */
    public function createViberAction(Request $request)
    {
        if ($request->getMethod() == "POST") {

            $senderNameData = $request->request->all();

            $senderNameData['type'] = "viber";

            $this->getApi()->anyPostMethod('sender-name', array_filter($senderNameData));

            return $this->redirectToRoute("sender-names");
        }

        if ($request->getMethod() == "GET") {
            $data = [
                'action' => $this->generateUrl("sender-name-viber-create"),
            ];

            return $this->render('sender-names/create-viber.html.twig', $data);
        }

        return $this->redirect("sender-names");
    }
}
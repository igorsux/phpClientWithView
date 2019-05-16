<?php

namespace App\Controller;

use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DetailsController extends AbstractController
{
    use ApiTrait;

    /**
     * @Route("/detatlization", name="detatlization")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function indexAction(Request $request)
    {
        $from = (new DateTime("2018-01-01"));
        $to = (new DateTime("2019-01-10"));
        $page = $request->get('page') ?? 1;
        $perPage = $request->get('perPage') ?? 10;

        $filter = [
            'senderName' => $request->get('senderName') ?? '',
            'type' => $request->get('type') ?? '',
            'status' => $request->get('status') ?? '',
            'sendSource' => $request->get('sendSource') ?? '',
            'operator' => $request->get('operator') ?? '',
            'country' => $request->get('country') ?? '',
            'paidType' => $request->get('paidType') ?? '',
            'phone' => $request->get('phone') ?? '',
            'text' => $request->get('text') ?? '',
        ];

        $requestData = [
            'fields' => 'all',
            'perPage' => $perPage,
            'page' => $page,
            'senderName' => $filter['senderName'] ?? '',
            'status' => $filter['status'] ?? '',
            'sendSource' => $filter['sendSource'] ?? '',
            'operator' => $filter['operator'] ?? '',
            'country' => $filter['country'] ?? '',
            'paidType' => $filter['paidType'] ?? '',
            'type' => $filter['type'] ?? '',
            'phone' => $filter['phone'] ?? '',
            'text' => $filter['text'] ?? '',
            'createdAtFrom' => $from->getTimestamp(),
            'createdAtTo' => $to->getTimestamp(),
        ];

        $response = $this->getApi()->anyGetMethod('details', $requestData);

        foreach ($response['items'] as $item) {
            $item['time'] = (new \DateTime())->setTimestamp($item['created']);
            $items[] = $item;
        }

        $data = [
            'items' => $items ?? [],
            'totalCount' => $response['total'],
            'page' => $page,
            'perPage' => $perPage,
            'filter' => $filter,
            'resetAction' => $this->generateUrl('detatlization'),
            'statuses' => $this->getStatuses(),
            'operators' => $this->getOperators(),
            'countries' => $this->getCountriesFilter(),
            'paidTypes' => $this->getPaidTypes(),
        ];

        return $this->render('details/index.html.twig', $data);
    }

    private function getPaidTypes()
    {
        return [
            'paid' => 'true',
            'free' => 'false'
        ];
    }
}
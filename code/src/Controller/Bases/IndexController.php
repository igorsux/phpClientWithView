<?php

namespace App\Controller\Bases;

use App\Controller\ApiTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    use ApiTrait;

    /**
     * @Route("/bases", name="bases")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getContactBases(Request $request)
    {
        $redsmsApi = $this->getApi();

        $requestData = [
            'fields' => 'all',
            'orderBy' => 'count',
            'orderDirection' => 'desc',
        ];

        $requestSearch = $request->get('search');

        $search = isset($requestSearch) ? $request->get('search') : "";
        $requestData['search'] = $search;
        if ($request->get('byContact')) {
            return $this->redirectToRoute('base-search', ['search' => $search]);
        }
        $response = $redsmsApi->getContactBases($requestData);

        $data = [
            'search' => $search,
            'bases' => $response['items'],
            'response' => $response,
        ];

        return $this->render('bases.html.twig', $data);
    }

    /**
     * @Route("/base/create", name="base-create")
     */
    public function createContactBase(Request $request)
    {
        $data = [
            "name" => "новая база",
            "color" => "#3e0651",
        ];

        $response = $this->getApi()->createBase($data);

        return $this->redirectToRoute('bases');
    }
}
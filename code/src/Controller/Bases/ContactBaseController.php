<?php

namespace App\Controller\Bases;

use DateTime;
use Throwable;
use App\Controller\ApiTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ContactBaseController extends AbstractController
{
    use ApiTrait;

    /**
     * @Route("/bases/{baseId}/", name="base")
     * @param $baseId
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function indexAction($baseId, Request $request)
    {
        $redsmsApi = $this->getApi();

        $requestSearch = $request->get('search');

        $search = isset($requestSearch) ? $request->get('search') : "";

        $base = $redsmsApi->getContactBase($baseId, ['fields' => 'all']);

        $mapping = $base['mapping'];

        $page = $request->get('page') ?? 1;

        $filter = [];
        $rFilter = [];
        foreach ($mapping as $map) {
            $rFilter[$map['name']] = [
                'var' => '',
                'expr' => 'contains',
            ];
        }
        if ($requestF = $request->get('filter')) {
            foreach ($requestF as $fname => $f) {
                if (isset($f['var2']) && $f['expr'] == 'range') {
                    $filter[] = [
                        'field' => $fname,
                        'expr' => '<',
                        'value' => $f['var2'],
                    ];
                    $filter[] = [
                        'field' => $fname,
                        'expr' => '>',
                        'value' => $f['var'],
                    ];
                    $rFilter[$fname] = [
                        'expr' => $f['expr'],
                        'var' => $f['var'],
                        'var2' => $f['var2'],
                    ];
                    continue;
                }
                if (isset($f['var2']) && $f['expr'] == 'ranges') {
                    $filter[] = [
                        'field' => $fname,
                        'expr' => '<=',
                        'value' => $f['var2'],
                    ];
                    $filter[] = [
                        'field' => $fname,
                        'expr' => '=2>',
                        'value' => $f['var'],
                    ];
                    $rFilter[$fname] = [
                        'expr' => $f['expr'],
                        'var' => $f['var'],
                        'var2' => $f['var2'],
                    ];
                    continue;
                }
                if ($fname == "gender" && in_array($f['var'], ['!=', '='])) {
                    $filter[] = [
                        'field' => $fname,
                        'expr' => $f['var'] == '=' ? 'exist' : 'missing',
                        'value' => "",
                    ];
                    $rFilter[$fname] = [
                        'expr' => $f['expr'],
                        'var' => $f['var'],
                    ];
                    continue;
                }
                if (isset($f['var']) && $f['var'] != "") {
                    $filter[] = [
                        'field' => $fname,
                        'expr' => $f['expr'],
                        'value' => $f['var'],
                    ];
                    $rFilter[$fname] = [
                        'expr' => $f['expr'],
                        'var' => $f['var'],
                    ];
                }
                if ($f['expr'] && in_array($f['expr'], ['exist', 'missing'])) {
                    $filter[] = [
                        'field' => $fname,
                        'expr' => $f['expr'],
                        'value' => "",
                    ];
                    $rFilter[$fname] = [
                        'expr' => $f['expr'],
                        'var' => "",
                    ];
                }

            }
        }

        if ($selectMapping = $request->get('check')) {
            $fieldsArray = [];
            foreach ($selectMapping as $key => $sm) {
                $fieldsArray[] = $key;
            }
        } else {
            $fieldsArray = ['id', 'email', 'phone', 'lastName'];
        }

        if ($sortingField = $request->get('sortingField')) {

        } else {
            $sortingField = 'lastName';
        }

        if ($sortingDirection = $request->get('sortingDirection')) {

        } else {
            $sortingDirection = 'asc';
        }

        $fields = implode(",", $fieldsArray);
        $dataRequest = [
            'fields' => $fields ?? "all",
            'perPage' => 10,
            'page' => $page,
            'orderBy' => $sortingField,
            'orderDirection' => $sortingDirection,
            'search' => $search,
            'filter' => json_encode($filter),
        ];

        if ($request->get('move')) {
            $methodUrl = 'contact-base-process';
            $dataRequest = [
                'sourceContactBaseId' => $baseId,
                'type' => 'move',
                'exceptContactIds' => '272652',
                'contactBaseId' => $baseId == 104 ? 107 : 104,
                'search' => $search,
                'filter' => json_encode($filter),
            ];

            $redsmsApi->anyPostMethod($methodUrl, $dataRequest);

            return $this->redirectToRoute('process');
        }

        if ($request->get('download')) {
            $redsmsApi->downloadBase($baseId, $dataRequest);
            die;
        }

        try {
            $start = microtime(true);
            $response = $redsmsApi->getContactByBase($baseId, $dataRequest);
            $end = microtime(true) - $start;
        } catch (Throwable $exception) {
            $error = [
                $dataRequest,
                $exception
            ];

            return $this->render('errorPage.html.twig', ['error' => $error]);
        }
        foreach ($response['items'] as $rItem) {
            foreach ($rItem as $name => $field) {
                if ($name == 'birthDate' && $field != "") {
                    $item[$name] = (new DateTime())->setTimestamp($field);
                } else {
                    $item[$name] = $field;
                }
            }
            $items[] = $item ?? [];
        }

        $data = [
            'search' => $search,
            'resetAction' => $this->generateUrl("base", ['baseId' => $baseId]),
            'baseId' => $baseId,
            'mapping' => $mapping,
            'fieldsArray' => $fieldsArray ?? [],
            'items' => $items ?? [],
            'totalCount' => $response['total'],
            'page' => $page,
            'perPage' => 10,
            'filter' => $rFilter,
            'request' => $dataRequest,
            'response' => $response,
            'sortingField' => $sortingField,
            'sortingDirection' => $sortingDirection,
            'endTime' => round($end, 3),
        ];

        return $this->render('contact-bases/index.html.twig', $data);
    }

    /**
     * @Route("/bases/{baseId}/delete", name="base-delete")
     * @param $baseId
     * @return Response
     */
    public function deleteAction($baseId)
    {
        $this->getApi()->anyDeleteMethod('contact-base/' . $baseId);

        return $this->redirectToRoute('bases');
    }

    /**
     * @Route("/testApiTest/{baseId}/{contactId}", name="contact")
     * @param $baseId
     * @param $contactId
     * @return Response
     */
    public function showContactController($baseId, $contactId)
    {
        $id = $baseId ?? 59;

        $redsmsApi = $this->getApi();
        $base = $redsmsApi->getContactBase($id, ['fields' => 'all']);

        $mapping = $base['mapping'];
        $contact = $redsmsApi->getContact($id, $contactId, ['fields' => 'all']);

        $data = [
            'baseId' => $baseId,
            'mapping' => $mapping,
            'contact' => $contact
        ];

        return $this->render('contact/index.html.twig', $data);
    }

    /**
     * @Route("/base/search", name="base-search")
     * @param Request $request
     * @return Response
     */
    public function searchAction(Request $request)
    {
        $redsmsApi = $this->getApi();

        $requestSearch = $request->get('search');

        $search = isset($requestSearch) ? $request->get('search') : "";

        $mapping = $redsmsApi->anyGetMethod('client/mapping')['mapping'];

        $page = $request->get('page') ?? 1;

        $filter = [];
        $rFilter = [];
        foreach ($mapping as $map) {
            $rFilter[$map['name']] = [
                'var' => '',
                'expr' => 'contains',
            ];
        }
        if ($requestF = $request->get('filter')) {
            foreach ($requestF as $fname => $f) {
                if (isset($f['var2']) && $f['expr'] == 'range') {
                    $filter[] = [
                        'field' => $fname,
                        'expr' => '<',
                        'value' => $f['var2'],
                    ];
                    $filter[] = [
                        'field' => $fname,
                        'expr' => '>',
                        'value' => $f['var'],
                    ];
                    $rFilter[$fname] = [
                        'expr' => $f['expr'],
                        'var' => $f['var'],
                        'var2' => $f['var2'],
                    ];
                    continue;
                }
                if (isset($f['var2']) && $f['expr'] == 'ranges') {
                    $filter[] = [
                        'field' => $fname,
                        'expr' => '<=',
                        'value' => $f['var2'],
                    ];
                    $filter[] = [
                        'field' => $fname,
                        'expr' => '=2>',
                        'value' => $f['var'],
                    ];
                    $rFilter[$fname] = [
                        'expr' => $f['expr'],
                        'var' => $f['var'],
                        'var2' => $f['var2'],
                    ];
                    continue;
                }
                if ($fname == "gender" && in_array($f['var'], ['!=', '='])) {
                    $filter[] = [
                        'field' => $fname,
                        'expr' => $f['var'] == '=' ? 'exist' : 'missing',
                        'value' => "",
                    ];
                    $rFilter[$fname] = [
                        'expr' => $f['expr'],
                        'var' => $f['var'],
                    ];
                    continue;
                }
                if ($f['var']) {
                    $filter[] = [
                        'field' => $fname,
                        'expr' => $f['expr'],
                        'value' => $f['var'],
                    ];
                    $rFilter[$fname] = [
                        'expr' => $f['expr'],
                        'var' => $f['var'],
                    ];
                }
                if ($f['expr'] && in_array($f['expr'], ['exist', 'missing'])) {
                    $filter[] = [
                        'field' => $fname,
                        'expr' => $f['expr'],
                        'value' => "",
                    ];
                    $rFilter[$fname] = [
                        'expr' => $f['expr'],
                        'var' => "",
                    ];
                }

            }
        }

        if ($selectMapping = $request->get('check')) {
            $fieldsArray = [];
            foreach ($selectMapping as $key => $sm) {
                $fieldsArray[] = $key;
            }
        } else {
            $fieldsArray = ['id', 'email', 'phone', 'lastName'];
        }

        if ($sortingField = $request->get('sortingField')) {

        } else {
            $sortingField = 'lastName';
        }

        if ($sortingDirection = $request->get('sortingDirection')) {

        } else {
            $sortingDirection = 'asc';
        }

        $fieldsArray[] = "baseId";
        $fields = implode(",", $fieldsArray);
        $dataRequest = [
            'fields' => $fields ?? "all",
            'perPage' => 10,
            'page' => $page,
            'orderBy' => $sortingField,
            'orderDirection' => $sortingDirection,
            'search' => $search,
            'filter' => $filter,
        ];

        try {
            $start = microtime(true);
            $response = $this->getApi()->anyGetMethod('contact-base/search', $dataRequest);
            $end = microtime(true) - $start;
        } catch (Throwable $exception) {
            $error = [
                $dataRequest,
                $exception
            ];

            return $this->render('errorPage.html.twig', ['error' => $error]);
        }

        $data = [
            'search' => $search,
            'resetAction' => $this->generateUrl("base-search"),
            'baseId' => 73,
            'mapping' => $mapping,
            'fieldsArray' => $fieldsArray ?? [],
            'items' => $response['items'],
            'totalCount' => $response['total'],
            'page' => $page,
            'perPage' => 10,
            'filter' => $rFilter,
            'request' => $dataRequest,
            'response' => $response,
            'sortingField' => $sortingField,
            'sortingDirection' => $sortingDirection,
            'endTime' => round($end, 3),
        ];

        return $this->render('contact-bases/index.html.twig', $data);
    }
}
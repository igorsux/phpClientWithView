<?php

namespace App\Controller\SenderName;

use App\Controller\ApiTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TemplateController extends AbstractController
{
    use ApiTrait;

    /**
     * @Route("/sender-name/templates", name="templates")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $requestData = [
            'perPage' => $request->get('perPage') ?? 10,
            'page' => $request->get('page') ?? 1,
            'fields' => 'all'
        ];

        if ($request->get('senderNameType')) {
            $requestData['senderNameType'] = $request->get('senderNameType');
        }

        $start = microtime(true);
        $response = $this->getApi()->anyGetMethod('sender-name/templates', $requestData);
        $end = microtime(true) - $start;

        $data = [
            'createAction' => $this->generateUrl("template-create"),
            'timeResponse' => round($end, 3) . " ms",
            'requestData' => $requestData,
            'response' => $response,
            'items' => $response['items'],
        ];

        return $this->render('sender-names/templates/index.html.twig', $data);
    }

    /**
     * @Route("/sender-name/templates/sms/{id}/show", name="template-show")
     * @param $id
     * @param Request $request
     */
    public function showAction($id, Request $request)
    {
        $type = $request->get('type') ?? 'sms';
dump($type);
        $template = $this->getApi()->anyGetMethod('sender-name/templates/'.$type.'/' . $id, ['fields' => 'all']);
        dd($template);
    }

    /**
     * @Route("/inn", name="inn-actin")
     * @param Request $request
     */
    public function innAction(Request $request)
    {
        if ($request->get('inn')) {
            $response = $this->getApi()->anyGetMethod('info/inn', ['inn' => $request->get('inn')]);
            dd($response);
        }
        dd("Пусто");
    }

    /**
     * @Route("/sender-name/templates/sms/create", name="template-create")
     */
    public function createAction()
    {
        $requestData = [
//            'operator' => 1,
            'senderName' => '246',
            'text' => "Текст two",
            'type' => "transact" // service
        ];

        $answer = $this->getApi()->createSenderNameSmsTemplate($requestData);

        dump($answer);

        dd('Создание');
    }

    /**
     * @Route("/sender-name/templates/sms/{id}/delete", name="template-delete")
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $this->getApi()->deleteSenderNameSmsTemplate($id);

        return $this->redirectToRoute("templates");
    }
}
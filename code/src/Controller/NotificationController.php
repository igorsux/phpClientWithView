<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotificationController extends AbstractController
{
    use ApiTrait;

    /**
     * @Route("/notifications", name="notifications")
     */
    public function getNotification()
    {
        $answer = $this->getApi()->getNotificationAccounts();

        $data = [
            'items' => $answer['items']
        ];

        return $this->render('notifications/index.html.twig', $data);
    }

    /**
     * @Route("/notifications/create", name="create-notifications")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        if ($request->getMethod() == "POST") {
            $data = [];
            $notificationList = [
                'aboutSubmitDispatch' => 'Подтверждение отправки',
                'aboutStartDispatch' => 'Старт рассылки',
                'aboutEndDispatch' => 'Завершение рассылки',
                'aboutBalance' => 'Пополнение баланса',
                'aboutCrash' => 'Сбой',
                'aboutLowBalance' => 'Мало средств на балансе',
            ];

            $notificationsData = $request->get('notifications') ?? [];
            foreach ($notificationList as $name => $title) {
                if (array_key_exists($name, $notificationsData)) {
                    $data[$name] = true;
                } else {
                    $data[$name] = false;
                }
            }

            $data['name'] = $request->get('name');
            $data['address'] = $request->get('address');
            $data['type'] = 'email';

            $this->getApi()->createNotifyAccount($data);

            return $this->redirectToRoute("notifications");
        }

        $account = [
            'name' => "",
            'type' => "",
            'address' => "",
            'notifications' => [
                'aboutSubmitDispatch' => '',
                'aboutStartDispatch' => '',
                'aboutEndDispatch' => '',
                'aboutBalance' => '',
                'aboutCrash' => '',
                'aboutLowBalance' => '',
            ],
        ];

        $data = [
            'account' => $account,
            'action' => $this->generateUrl("create-notifications")
        ];

        return $this->render('notifications/edit.html.twig', $data);
    }

    /**
     * @Route("/notifications/{id}/edit", name="edit-notifications")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        if ($request->getMethod() == "POST") {
            $data = [];

            $notificationList = [
                'aboutSubmitDispatch' => 'Подтверждение отправки',
                'aboutStartDispatch' => 'Старт рассылки',
                'aboutEndDispatch' => 'Завершение рассылки',
                'aboutBalance' => 'Пополнение баланса',
                'aboutCrash' => 'Сбой',
                'aboutLowBalance' => 'Мало средств на балансе',
            ];

            $notificationsData = $request->get('notifications') ?? [];
            foreach ($notificationList as $name => $title) {
                if (array_key_exists($name, $notificationsData)) {
                    $data[$name] = true;
                } else {
                    $data[$name] = false;
                }
            }

            $data['name'] = $request->get('name');
            $data['address'] = $request->get('address');
            $data['type'] = 'email';

            $this->getApi()->editNotificationAccount($id, $data);

            return $this->redirectToRoute("notifications");
        }


        $account = $this->getApi()->getNotificationAccount($id);

        $data = [
            'account' => $account,
            'action' => $this->generateUrl("edit-notifications", ['id' => $id])
        ];

        return $this->render('notifications/edit.html.twig', $data);
    }

    /**
     * @Route("/notifications/{id}/delete", name="delete-notifications")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $this->getApi()->deleteNotifyAccount($id);

        return $this->redirectToRoute("notifications");
    }
}
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class anotherController extends AbstractController
{
    /**
     * @Route("/kek", name="index-kek")
     */
    public function getNotification()
    {

        return new Response('<html><body>kek</body></html>');
    }

}
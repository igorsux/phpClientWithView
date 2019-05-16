<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\HttpFoundation\Response;

class ProcessController extends AbstractController
{
    use ApiTrait;

    /**
     * @Route("/process", name="process")
     * @return Response
     */
    public function indexAction()
    {
       $answer =  $this->getApi()->anyGetMethod('contact-base-process');

       $data = [
         'items' => $answer['items']
       ];

        return $this->render('process/index.html.twig', $data);
    }

    /**
     * @Route("/process/{id}/delete", name="process-delete")
     * @param $id
     * @return Response
     */
    public function deleteAction($id)
    {
        $this->getApi()->anyDeleteMethod('contact-base-process/'.$id);

        return $this->redirectToRoute('process');
    }
}
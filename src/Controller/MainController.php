<?php
// src/Controller/MainController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @return Response
     */
    public function index()
    {
        $user=$this->getUser();
        $date = date('Y-m-d');
        //check if user is connected
        if(!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        }
        else{
            $em = $this->getDoctrine()->getManager();
            $events = $em->getRepository('App:Event')->findByDate($date);
            $users = $em->getRepository('App:User')->findAll();

            return $this->render('main/body.html.twig', array(
                'user' => $user,
                'events' => $events,
                'users' => $users
            ));
        }
    }

}

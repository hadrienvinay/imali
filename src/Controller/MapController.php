<?php
// src/Controller/MapController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    /**
     * @return Response
     */
    public function map()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('App:User')->findAll();
        //select all lat and long of users address
        $allPos = array(array(NULL));
        $userName = array(NULL);
        $arrContextOptions = array(
                   "ssl" => array(
                       "verify_peer" => false,
                       "verify_peer_name" => false,
                   ),
               );
        $geocoder = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyA0wuGfkqLD67jR6NfcC8mm4EuUROGis_I&address=%s&sensor=false";
        $i = 0;
        // Get latitude and longitude of the users address
        foreach($users as $user){
            if(!empty($user->getAddress())){
                $query = sprintf($geocoder, urlencode(utf8_encode($user->getAddress())));
                $result = json_decode(file_get_contents($query, false, stream_context_create($arrContextOptions)));

                if (empty($result->results)) {
                    $latitude = 0;
                    $longitude = 0;
                } else {
                    $json = $result->results[0];
                    $latitude = (float)$json->geometry->location->lat;
                    $longitude = (float)$json->geometry->location->lng;
                }
                $allPos[0][$i] = $latitude;
                $allPos[1][$i] = $longitude;
                $userName[$i] = $user->getUsername();
                $i++;
            }
        }
        return $this->render('my/map.html.twig', array(
            'users' => $users,
            'userName' => $userName,
            'allPos' => $allPos
        ));
    }
    
    /**
     * @return Response
     */
    public function mapJob()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('App:User')->findAll();
        //select all lat and long of users address
        $allPos = array(array(NULL));
        $userName = array(NULL);
        $arrContextOptions = array(
                   "ssl" => array(
                       "verify_peer" => false,
                       "verify_peer_name" => false,
                   ),
               );
        $geocoder = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyA0wuGfkqLD67jR6NfcC8mm4EuUROGis_I&address=%s&sensor=false";
        $i = 0;
        // Get latitude and longitude of the users address
        foreach($users as $user){
            if(!empty($user->getJob() and $user->getJob()->getAddress())){
                $query = sprintf($geocoder, urlencode(utf8_encode($user->getJob()->getAddress())));
                $result = json_decode(file_get_contents($query, false, stream_context_create($arrContextOptions)));

                if (empty($result->results)) {
                    $latitude = 0;
                    $longitude = 0;
                } else {
                    $json = $result->results[0];
                    $latitude = (float)$json->geometry->location->lat;
                    $longitude = (float)$json->geometry->location->lng;
                }
                $allPos[0][$i] = $latitude;
                $allPos[1][$i] = $longitude;
                $userName[$i] = $user->getUsername() . " - " . $user->getJob()->getCompanyName();
                $i++;
            }
        }
        return $this->render('my/map_job.html.twig', array(
            'users' => $users,
            'userName' => $userName,
            'allPos' => $allPos
        ));
    }

}
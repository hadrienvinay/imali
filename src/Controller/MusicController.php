<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use YoutubeDl\YoutubeDl;
use YoutubeDl\Exception\CopyrightException;
use YoutubeDl\Exception\NotFoundException;
use YoutubeDl\Exception\PrivateVideoException;
use App\Form\ConvertorType;
use App\Entity\Song;
use App\Form\SongType;

class MusicController extends Controller
{
    /**
     * @Route("/music", name="music")
     */
    public function radio(Request $request)
    {
        $user=$this->getUser();
        //check if user is connected
        if(!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        }
        else{
        $notifiableRepo = $this->get('doctrine.orm.entity_manager')->getRepository('MgiletNotificationBundle:NotifiableNotification');
        $notifiableEntityRepo = $this->get('doctrine.orm.entity_manager')->getRepository('MgiletNotificationBundle:NotifiableEntity');
        $notifiable = $notifiableEntityRepo->findOneby(array("identifier" => $user));
        $notificationList = $notifiableRepo->findAllForNotifiableId($notifiable);
        
        $em = $this->getDoctrine()->getManager();
        $songs = $em->getRepository('App:Song')->findAll();
            
        $song = new Song();
        $form = $this->get('form.factory')->create(SongType::class, $song);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $songFile = $form['file']->getData();
            dump($songFile);
            $fileName = $songFile->getClientOriginalName();
            //$fileName = $file.'.'.$file->guessExtension();

           $songFile->move(
                $this->getParameter('download_directory'),
                $fileName
            );
            $song->setFileName($fileName);
            $song->setTitle("a");
            $song->setArtist("b");
            $song->setUploader($user);
            $song->setCreatedDate(new \DateTime());
            $em->persist($song);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Morceau ajouté avec succès !');

            return $this->redirectToRoute('radio');
        }
        return $this->render('my/music/radio.html.twig', array(
            'notificationList' => $notificationList,
            'songs' => $songs,
            'form' => $form->createView(),
             ));
        }
    }
    
    
    public function convertor(Request $request)
    {
        $user = $this->getUser();
        $notifiableRepo = $this->get('doctrine.orm.entity_manager')->getRepository('MgiletNotificationBundle:NotifiableNotification');
        $notifiableEntityRepo = $this->get('doctrine.orm.entity_manager')->getRepository('MgiletNotificationBundle:NotifiableEntity');
        $notifiable = $notifiableEntityRepo->findOneby(array("identifier" => $user));
        $notificationList = $notifiableRepo->findAllForNotifiableId($notifiable);
       
        $formBuilder = $this->get('form.factory')->createBuilder(ConvertorType::class);  
        
        $form = $formBuilder->getForm();
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            
            $url = $form['url']->getData();
            $dl = new YoutubeDl([
                'extract-audio' => true,
                'audio-format' => 'mp3',
                'audio-quality' => 0, // best
                'output' => '%(title)s.%(ext)s',
            ]);
            // For more options go to https://github.com/rg3/youtube-dl#user-content-options

            $dl->setDownloadPath($this->getParameter('download_directory'));
            $dl->setBinPath('C:\wamp64\youtube-dl');        
            $filePath=false;
            try {
                $video = $dl->download($url);
                $filePath = $this->getParameter('download_directory').'/'.$video->getTitle().'.mp3';
                //$video->getFile(); // \SplFileInfo instance of downloaded file
            } catch (NotFoundException $e) {
                // Video not found
                $error = "Cette vidéo n'existe pas";
            } catch (PrivateVideoException $e) {
                // Video is private
                $error = "Cette vidéo est privée";

            } catch (CopyrightException $e) {
                // The YouTube account associated with this video has been terminated due to multiple third-party notifications of copyright infringement
                $error = "Copyright Error";
                
            } catch (\Exception $e) {
                // Failed to download
                $error = "Erreur lors du téléchargement";
            }
                        
            if($filePath){
                $request->getSession()->getFlashBag()->add('success', 'Vidéo convertie avec succès !');
                return $this->file($filePath);
            }else{
                $request->getSession()->getFlashBag()->add('danger', $error);
                return $this->RedirectToRoute('convertor');
            }

        }
        
        
        return $this->render('my/music/convertor.html.twig',array(
            'notificationList' => $notificationList,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @return Response
     */
    public function addSong(Request $request)
    {
        //check if user is connected
        $user=$this->getUser();
        if(!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        }
        else{
            $notifiableRepo = $this->get('doctrine.orm.entity_manager')->getRepository('MgiletNotificationBundle:NotifiableNotification');
            $notifiableEntityRepo = $this->get('doctrine.orm.entity_manager')->getRepository('MgiletNotificationBundle:NotifiableEntity');
            $notifiable = $notifiableEntityRepo->findOneby(array("identifier" => $user));
            $notificationList = $notifiableRepo->findAllForNotifiableId($notifiable);
            
            $song = new Song();
            $em = $this->getDoctrine()->getManager();

            $form = $this->get('form.factory')->create(SongType::class, $song);
            
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                
                $songFile = $form['file']->getData();
                dump($songFile);
                $fileName = $songFile->getClientOriginalName();
                //$fileName = $file.'.'.$file->guessExtension();

               $songFile->move(
                    $this->getParameter('download_directory'),
                    $fileName
                );
                $song->setFileName($fileName);
                $song->setTitle("a");
                $song->setArtist("b");
                $song->setUploader($user);
                $song->setCreatedDate(new \DateTime());
                $em->persist($song);
                $em->flush();

                $request->getSession()->getFlashBag()->add('success', 'Morceau ajouté avec succès !');

                return $this->redirectToRoute('radio');
            }

            return $this->render('add/song.html.twig', array(
                'user' => $user,
                'form' => $form->createView(),
                'notificationList' => $notificationList
            ));
        }
    }
    
    /**
     * @return Response
     */
    public function deleteSong(Request $request, $id)
    {
        //check if user is connected
        $user=$this->getUser();
        if(!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        }
        else{
            $em = $this->getDoctrine()->getManager();
            $song = $em->getRepository('App:Song')->find($id);

            // Security check
            if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                // else error page
                throw new AccessDeniedException('Accès limité aux admins narvaloo.');
            }
            
            if (!$song) {
                throw $this->createNotFoundException('Aucun son trouvé pour id: ' . $song);
            }

            $em->remove($song);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Morceau supprimé avec succès !');

            return $this->redirectToRoute('radio');
        }
    }
}

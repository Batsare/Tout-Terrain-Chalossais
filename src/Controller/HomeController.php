<?php

namespace App\Controller;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

Class HomeController extends Controller
{
    public function indexAction(){
        return $this->render('home/index.html.twig');
    }

    public function facebookAction(){
        $fb = new Facebook([
            'app_id' => '2116714391894730',
            'app_secret' => '4b39e425935d9aa10ca82b63110b01d1',
            'default_graph_version' => 'v2.10',
            //'default_access_token' => '{access-token}', // optional
        ]);


        //var_dump($test);die();
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get(
                '/cadetsdechalosse/posts',
                '2116714391894730|7ilE0SD5Zp2-kxovKng8HR0ypNc'
            );
        } catch(FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        //var_dump($response.data);die();
        $graphNode = $response->getGraphEdge();
        //var_dump($graphNode);die();
        return $this->render('home/facebook.html.twig',
            array('fb' => $graphNode)
        );
    }

    public function guestbookAction(){

    }
}
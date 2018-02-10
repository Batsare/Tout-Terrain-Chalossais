<?php
namespace App\Controller;

use App\Entity\Post;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PostController
{
    public function indexAction(Environment $twig,RegistryInterface $doctrine)
    {
        $postsNews = $doctrine->getRepository(Post::class)->findAll();


        return new Response($twig->render('post/index.html.twig', [
            'postsNews' => $postsNews
        ]));
    }

    public function viewAction()
    {

    }

    public function addAction()
    {

    }

    public function deleteAction()
    {

    }


}
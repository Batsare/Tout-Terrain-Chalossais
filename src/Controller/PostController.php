<?php
namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Type\PostType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PostController
{
    public function indexAction(Environment $twig,RegistryInterface $doctrine,PostRepository $postRepository)
    {
        $postsNews = $doctrine->getRepository(Post::class)->findBy([],['id' => 'DESC'], 4, 0);
        $postsNews = $postRepository->postPublished();

        return new Response($twig->render('post/index.html.twig', [
            'postsNews' => $postsNews
        ]));
    }

    public function viewAction($id, Environment $twig, RegistryInterface $doctrine)
    {
        $post = $doctrine->getRepository(Post::class)->find($id);

        return new Response($twig->render('post/view.html.twig', [
            'postsNews' => $post
        ]));
    }

    public function addAction(PostRepository $postRepository, RedirectController $redirectController, FormFactoryInterface $formFactory, Environment $twig, RegistryInterface $doctrine, Request $request)
    {
        $post = New Post();

        $form = $formFactory->create(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getEntityManager()->persist($post);
            $doctrine->getEntityManager()->flush();

            $postRepository->lastPostForArchive();



            // On redirige vers la page de visualisation de l'annonce nouvellement créée

            return $redirectController->redirectAction($request, 'post_home');
        }
        return new Response($twig->render('post/add.html.twig',[
            'form' => $form->createView()
        ]));
    }

    public function deleteAction($id, PostRepository $postRepository, RedirectController $redirectController, Request $request)
    {
        $postRepository->deleteById($id);
        return $redirectController->redirectAction($request, 'post_home');
    }

    public function archiveAction(){

    }

}
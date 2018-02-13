<?php
namespace App\Controller;

use App\Entity\Post;
use App\Type\PostType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PostController
{
    public function indexAction(Environment $twig,RegistryInterface $doctrine)
    {
        $postsNews = $doctrine->getRepository(Post::class)->findBy([],['id' => 'DESC']);


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

    public function addAction(RedirectController $redirectController, FormFactoryInterface $formFactory, Environment $twig, RegistryInterface $doctrine, Request $request)
    {
        $post = New Post();

        $form = $formFactory->create(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getEntityManager()->persist($post);
            $doctrine->getEntityManager()->flush();



            // On redirige vers la page de visualisation de l'annonce nouvellement créée

            return $redirectController->redirectAction($request, 'post_home');
        }
        return new Response($twig->render('post/add.html.twig',[
            'form' => $form->createView()
        ]));
    }

    public function deleteAction()
    {

    }


}
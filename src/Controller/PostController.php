<?php
namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Type\PostType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PostController extends Controller
{
    public function indexAction(Environment $twig,PostRepository $postRepository)
    {
        //$postsNews = $doctrine->getRepository(Post::class)->findBy([],['id' => 'DESC'], 4, 0);
        $postsNews = $postRepository->postPublished();

        return new Response($twig->render('post/index.html.twig', [
            'postsNews' => $postsNews
        ]));
    }

    public function viewAction(Post $post, Environment $twig)
    {

        return new Response($twig->render('post/view.html.twig', [
            'post' => $post
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

    public function editAction(Post $post, Request $request,Environment $twig, FormFactoryInterface $formFactory, RegistryInterface $registry)
    {
        $form = $formFactory->create(PostType::class, $post);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            // Inutile de persister ici, Doctrine connait déjà notre annonce

            $registry->getEntityManager()->flush();

            //$request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

            //return $redirectResponse->redirectToRoute('post_view', array('id' => $post->getId()));
            return $this->redirectToRoute('post_view',array('id'=> $post->getId()));

        }
        return $this->render('post/edit.html.twig', array(
            'post' => $post,
            'form'   => $form->createView(),
        ));
    }


    public function deleteAction(Post $post,RegistryInterface $doctrine,FormFactoryInterface $formFactory, Environment $twig, RedirectController $redirectController, Request $request)
    {
        $em = $doctrine->getManager();
        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $formFactory->create();
        if ($request->isMethod('POST')) {
            $em->remove($post);
            $em->flush();
            //$request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");
            return $redirectController->redirectAction($request, 'post_home');
        }
        return new Response($twig->render('post/delete.html.twig', array(
            'post' => $post,
            'form'   => $form->createView(),
        )));
    }

    public function archiveAction(){

    }

    /**
     * @ParamConverter("json")
     */
    public function ParamConverterAction($json)
    {
        return new Response(print_r($json, true));
    }


}
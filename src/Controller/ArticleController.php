<?php
namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Type\ArticleType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ArticleController extends Controller
{
    public function indexAction(Environment $twig, ArticleRepository $postRepository, $page)
    {
        //$postsNews = $doctrine->getRepository(Post::class)->findBy([],['id' => 'DESC'], 4, 0);
        //$postsNews = $postRepository->postPublished();

        /**
         * Pagination
         */
        $limit = 4;
        if($page != 1) {
            $pageSQL = $page;
            $offset = $limit * ($page-1);
        } else {
            $pageSQL = 1;
            $offset = 0;
        }
        $pagerfanta = $postRepository->findLatest($page);

        $postsNews = $postRepository->findBy([],['date' => 'DESC'], $limit, $offset);



        return new Response($twig->render('article/index.html.twig', [
            'postsNews' => $postsNews,
            'my_pager' => $pagerfanta
        ]));
    }

    public function viewAction(Article $post, Environment $twig)
    {

        return new Response($twig->render('article/view.html.twig', [
            'article' => $post
        ]));
    }

    public function addAction(ArticleRepository $postRepository, RedirectController $redirectController, FormFactoryInterface $formFactory, Environment $twig, RegistryInterface $doctrine, Request $request)
    {
        $post = New Article();

        $form = $formFactory->create(ArticleType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getEntityManager()->persist($post);
            $doctrine->getEntityManager()->flush();

            $postRepository->lastPostForArchive();



            // On redirige vers la page de visualisation de l'annonce nouvellement créée

            return $redirectController->redirectAction($request, 'article_home');
        }
        return new Response($twig->render('article/add.html.twig',[
            'form' => $form->createView()
        ]));
    }

    public function editAction(Article $post, Request $request,Environment $twig, FormFactoryInterface $formFactory, RegistryInterface $registry)
    {
        $form = $formFactory->create(ArticleType::class, $post);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            // Inutile de persister ici, Doctrine connait déjà notre annonce

            $registry->getEntityManager()->flush();

            //$request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

            //return $redirectResponse->redirectToRoute('post_view', array('id' => $article->getId()));
            return $this->redirectToRoute('article_view',array('id'=> $post->getId()));

        }
        return $this->render('article/edit.html.twig', array(
            'article' => $post,
            'form'   => $form->createView(),
        ));
    }


    public function deleteAction(Article $post,RegistryInterface $doctrine,FormFactoryInterface $formFactory, Environment $twig, RedirectController $redirectController, Request $request)
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
        return new Response($twig->render('article/delete.html.twig', array(
            'article' => $post,
            'form'   => $form->createView(),
        )));
    }

    public function homeAction($limit, ArticleRepository $postRepository){
        $em = $this->getDoctrine()->getManager();

        $lastArticles = $postRepository->findBy(
            array(),
            array('date' => 'DESC'),
            $limit,
            0
        );

        return $this->render('article/home.html.twig', array(
            'articles' => $lastArticles
        ));


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
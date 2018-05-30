<?php

namespace App\Controller;

use App\Entity\Guestbook;
use App\Entity\GuestbookPrivate;
use App\Repository\GuestbookPrivateRepository;
use App\Type\GuestbookPrivateType;
use App\Type\GuestbookType;
use App\Repository\GuestbookRepository;
use Doctrine\ORM\Query\AST\GeneralCaseExpression;
use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class GuestbookController extends Controller
{
    public function indexAction($page, GuestbookRepository $guestbookRepository, Request $request, Environment $twig, RegistryInterface $doctrine, FormFactoryInterface $formFactory)
    {
        $guestbook = new Guestbook();

        $form = $formFactory->create(GuestbookType::class, $guestbook);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getEntityManager()->persist($guestbook);
            $doctrine->getEntityManager()->flush();

            unset($guestbook);
            unset($form);
            $guestbook = new Guestbook();
            $form = $formFactory->createBuilder(FormType::class, $guestbook)
                ->add('author', TextType::class)
                ->add('content', FroalaEditorType::class)
                ->add('save', SubmitType::class)
                ->getForm();
        }


        /**
         * Pagination
         */
        $limit = 10;
        if($page != 1) {
            $pageSQL = $page;
            $offset = $limit * ($page-1);
        } else {
            $pageSQL = 1;
            $offset = 0;
        }
        $pagerfanta = $guestbookRepository->findLatest($page);

        $postsGuestbook = $doctrine->getRepository(Guestbook::class)->findBy([],['postedAt' => 'DESC'], $limit, $offset);


        return new Response($twig->render('guestbook/index.html.twig', [
            'postsGuestbook' => $postsGuestbook,
            'form' => $form->createView(),
            'my_pager' => $pagerfanta
        ]));
    }

    public function widgetAction($limit, GuestbookRepository $guestbookRepository){
        $lastMessage = $guestbookRepository->findBy(
            array(),
            array('id' => 'DESC'),
            $limit,
            0
        );

        return $this->render('guestbook/widget.html.twig',
            array('messages' => $lastMessage)
        );
    }

    /**
     * @Route("/guestbookprivate/{page}", name="guestbook_private", requirements={"page"="\d+"})
     * @Security("has_role('ROLE_BUREAU')")
     */
    public function indexPrivateAction($page = 1, GuestbookPrivateRepository $guestbookPrivateRepository, Request $request, Environment $twig, RegistryInterface $doctrine, FormFactoryInterface $formFactory)
    {
        $guestbook = new GuestbookPrivate();

        $form = $formFactory->create(GuestbookPrivateType::class, $guestbook);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getEntityManager()->persist($guestbook);
            $doctrine->getEntityManager()->flush();

            unset($guestbook);
            unset($form);
            $guestbook = new Guestbook();
            $form = $formFactory->createBuilder(FormType::class, $guestbook)
                ->add('author', TextType::class)
                ->add('content', FroalaEditorType::class)
                ->add('save', SubmitType::class)
                ->getForm();
        }


        /**
         * Pagination
         */
        $limit = 10;
        if($page != 1) {
            $pageSQL = $page;
            $offset = $limit * ($page-1);
        } else {
            $pageSQL = 1;
            $offset = 0;
        }
        $pagerfanta = $guestbookPrivateRepository->findLatest($page);

        $postsGuestbook = $doctrine->getRepository(GuestbookPrivate::class)->findBy([],['postedAt' => 'DESC'], $limit, $offset);


        return new Response($twig->render('guestbookprivate/index.html.twig', [
            'postsGuestbook' => $postsGuestbook,
            'form' => $form->createView(),
            'my_pager' => $pagerfanta
        ]));
    }

}
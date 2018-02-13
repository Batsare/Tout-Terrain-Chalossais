<?php

namespace App\Controller;

use App\Entity\Guestbook;
use App\Type\GuestbookType;
use App\Repository\GuestbookRepository;
use Doctrine\ORM\Query\AST\GeneralCaseExpression;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class GuestbookController
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
                ->add('content', CKEditorType::class, array(
                    'config' => array('toolbar' => 'full')))
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


}
<?php

namespace App\Controller;

use App\Entity\Guestbook;
use Doctrine\ORM\Query\AST\GeneralCaseExpression;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
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
    public function indexAction(Request $request, Environment $twig, RegistryInterface $doctrine, FormFactoryInterface $formFactory)
    {
        $guestbook = new Guestbook();

        $form = $formFactory->createBuilder(FormType::class, $guestbook)
            ->add('author', TextType::class)
            ->add('content', CKEditorType::class, array(
                'config' => array('toolbar' => 'full')))
            ->add('save', SubmitType::class)
            ->getForm();

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

        $postsGuestbook = $doctrine->getRepository(Guestbook::class)->findBy([],['postedAt' => 'DESC']);

        return new Response($twig->render('guestbook/index.html.twig', [
            'postsGuestbook' => $postsGuestbook,
            'form' => $form->createView()
        ]));
    }


}
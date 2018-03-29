<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Type\GalleryType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

Class GalleryController extends Controller
{
    public function indexAction()
    {

    }

    public function addAction(FormFactoryInterface $formFactory, Request $request, RegistryInterface $doctrine, RedirectController $redirectController, Environment $twig)
    {
        $gallery = New Gallery();
        $photo = New Photo();

        $gallery->addPhoto($photo);

        $form = $formFactory->create(GalleryType::class, $gallery);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getEntityManager()->persist($gallery);
            $doctrine->getEntityManager()->flush();

            // On redirige vers la page de visualisation de l'annonce nouvellement créée

            return $redirectController->redirectAction($request, 'gallery_home');
        }
        return new Response($twig->render('gallery/add.html.twig',[
            'form' => $form->createView()
        ]));
    }

    public function deleteAction()
    {

    }

    public function viewAction()
    {

    }
}
<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Type\GalleryType;
use App\Type\PhotoType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use function Symfony\Component\Debug\Tests\testHeader;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

Class GalleryController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $galleries = $em->getRepository('App:Gallery')->findAll();

        return new Response($this->renderView('gallery/index.html.twig',array(
            'galleries' => $galleries
        )));
    }

    public function addAction(FormFactoryInterface $formFactory, Request $request, RedirectController $redirectController, Environment $twig)
    {
        $gallery = New Gallery();
        $photo = New Photo();
        //$gallery->addPhotos($photo);



        $form = $formFactory->create(PhotoType::class, $photo);

        $form->handleRequest($request);


        if ($request->getMethod() != 'GET' && $form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($photo->getFiles() as $file){
                $photo = new Photo();
                $photo->setFile($file);
                $photo->setGallery($form->getData()->getGallery());
                $em->persist($photo);
            }

            //$em->persist($gallery);
            $em->flush();

            // On redirige vers la page de visualisation de l'annonce nouvellement créée

            return $redirectController->redirectAction($request, 'gallery_home');
        }
        return new Response($this->renderView('gallery/add.html.twig', [
            'form' => $form->createView()
        ]));
    }

    public function deleteAction()
    {

    }

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $photos = $em->getRepository('App:Photo')->findBy(array('gallery' => $id));
        $gallery = $em->getRepository('App:Gallery')->find($id);
        return new Response($this->renderView('gallery/view.html.twig',array(
            'photos' => $photos,
            'gallery' => $gallery,
        )));

    }
}
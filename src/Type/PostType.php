<?php

namespace App\Type;
;
use App\Repository\CategoryRepository;
use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Arbitrairement, on récupère toutes les catégories qui commencent par "D"


        $builder
            ->add('date',      DateTimeType::class)
            ->add('title',     TextType::class)
            ->add('author',    TextType::class)
            ->add('content', FroalaEditorType::class, array(
                "language" => "fr",
                "tableColors" => [ "#FFFFFF", "#FF0000" ],
                "saveParams" => [ "id" => "myEditorField" ]
            ))
            ->add('image',     ImageType::class)
            ->add('categories', EntityType::class, array(
                'class'         => 'App:Category',
                'choice_label'  => 'name',
                'multiple'      => true,
                'query_builder' => function(CategoryRepository $repository) {
                    return $repository->getLikeQueryBuilder();
                }
            ))
            ->add('published', CheckboxType::class, array(
                'label' => 'Publier',
                'required' => false))
            ->add('save',      SubmitType::class, array(
                'label' => 'Envoyer'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Post'
        ));
    }
}

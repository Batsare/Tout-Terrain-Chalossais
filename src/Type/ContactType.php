<?php
namespace App\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'constraints' => array(
                    new NotBlank(array("message" => "Veuillez saisir votre nom")),
                )
            ))
            ->add('subject', TextType::class, array(
                'constraints' => array(
                    new NotBlank(array("message" => "Veuillez saisir le sujet de votre message")),
                )
            ))
            ->add('email', EmailType::class, array(
                'constraints' => array(
                    new NotBlank(array("message" => "Veuillez saisir votre email")),
                    new Email(array("message" => "Votre adresse mail n'est pas valide")),
                )
            ))
            ->add('message', TextareaType::class, array(
                'constraints' => array(
                    new NotBlank(array("message" => "Veuillez saisir votre message")),
                )
            ))
            ->add('save', SubmitType::class, array('label' => 'Envoyer'))
        ;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'error_bubbling' => true
        ));
    }

    public function getName()
    {
        return 'contact_form';
    }
}
<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

class ForgetPasswordPartnerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => [
                    'placeholder' => 'Entrez votre adresse email',
                    'class' => 'form-control',
                ],
                
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une adresse email.']),
                    new EmailConstraint(['message' => 'Veuillez entrer une adresse email valide.']),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Réinitialiser le mot de passe',
                'attr' => ['class' => 'btn btn-primary w-100'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Pas besoin de lier ce formulaire à une entité
            'data_class' => null,
        ]);
    }
}
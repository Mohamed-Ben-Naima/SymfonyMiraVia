<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginPartnerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Entrez votre email'],
                'label' => 'Email',
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['placeholder' => 'Entrez votre mot de passe'],
                'label' => 'Mot de passe',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true, // Active la protection CSRF
            'csrf_field_name' => '_token', // Nom du champ CSRF
            'csrf_token_id' => 'login_partner', // Identifiant unique pour le token CSRF
        ]);
    }
}
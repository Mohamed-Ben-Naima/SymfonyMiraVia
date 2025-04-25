<?php
// src/Form/RegistrationFormType.php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegistrationFormType2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Adresse email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une adresse email valide.',
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom de famille',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre nom de famille.',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre prénom.',
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'label' => 'Date de naissance',
                'required' => false,
            ])
            ->add('adresseUtilisateur', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Adresse',
                'required' => false,
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le mot de passe est obligatoire. Veuillez en saisir un.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 4096,
                        'maxMessage' => 'Votre mot de passe est trop long. Veuillez entrer un mot de passe de moins de {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les conditions d\'utilisation',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Pour continuer, vous devez accepter nos conditions d\'utilisation.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
            'attr' => [
                'novalidate' => 'novalidate',
                'class' => 'needs-validation',
            ],
        ]);
    }
}
<?php
// src/Form/RegistrationFormType2Type.php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class RegistrationFormType2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'votre@email.com'
                ],
                'label' => 'Adresse email',
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse email est requise.']),
                    new Email(['message' => 'Veuillez entrer une adresse email valide (ex: votre@email.com).']),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'L\'email ne doit pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre nom'
                ],
                'label' => 'Nom de famille',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom de famille est requis.']),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre prénom'
                ],
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est requis.']),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le prénom ne doit pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: +33612345678'
                ],
                'label' => 'Téléphone',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\+?[1-9]\d{1,14}$/',
                        'message' => 'Le numéro de téléphone doit être au format international (ex: +33612345678).',
                        'match' => true,
                        'normalizer' => fn (?string $value) => $value === null ? null : trim($value)
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le numéro de téléphone ne doit pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'AAAA-MM-JJ'
                ],
                'label' => 'Date de naissance',
                'required' => false,
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date de naissance ne peut pas être dans le futur.'
                    ]),
                ],
            ])
            ->add('adresseUtilisateur', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre adresse'
                ],
                'label' => 'Adresse',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'L\'adresse ne doit pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                    'placeholder' => 'Minimum 8 caractères'
                ],
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank(['message' => 'Le mot de passe est requis.']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 255,
                        'maxMessage' => 'Le mot de passe ne doit pas dépasser {{ limit }} caractères.'
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                        'message' => 'Le mot de passe doit inclure une lettre majuscule, une lettre minuscule et un chiffre.'
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les conditions d\'utilisation',
                'constraints' => [
                    new IsTrue(['message' => 'Vous devez accepter les conditions d\'utilisation pour continuer.']),
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
            'constraints' => [
                new UniqueEntity([
                    'fields' => 'email',
                    'message' => 'Cet email est déjà utilisé. Essayez un autre ou connectez-vous.',
                ]),
                new UniqueEntity([
                    'fields' => 'telephone',
                    'message' => 'Ce numéro de téléphone est déjà enregistré. Veuillez en utiliser un autre.',
                    'ignoreNull' => true,
                ]),
            ],
        ]);
    }
}
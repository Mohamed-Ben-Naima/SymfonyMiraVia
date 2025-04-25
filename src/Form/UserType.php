<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email est obligatoire']),
                    new Assert\Email(['message' => 'Veuillez entrer un email valide']),
                    new Assert\Length([
                        'max' => 180,
                        'maxMessage' => 'L\'email ne doit pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est obligatoire']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le prénom ne doit pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'options' => ['attr' => ['autocomplete' => 'new-password']],
                'required' => false,
                'first_options'  => [
                    'label' => 'Nouveau mot de passe',
                    'constraints' => [
                        new Assert\Length([
                            'min' => 12,
                            'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                            'max' => 4096
                        ])
                    ]
                ],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'mapped' => false
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\+?[0-9]{8,15}$/',
                        'message' => 'Numéro de téléphone invalide'
                    ])
                ]
            ])
            ->add('avatar', FileType::class, [
                'label' => 'Avatar',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG ou WebP)'
                    ])
                ]
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => '-18 years',
                        'message' => 'Vous devez avoir au moins 18 ans'
                    ])
                ]
            ])
            ->add('adresse_utilisateur', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'L\'adresse ne doit pas dépasser {{ limit }} caractères'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'user_profile',
        ]);
    }
}
<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use App\Entity\User;

class SigUpPartnerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Entrez votre prénom'],
                'property_path' => 'prenom', // Mappe le champ "first_name" à la propriété "prenom" de l'entité
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire.']),
                    new Length(['max' => 50, 'maxMessage' => 'Le prénom ne peut pas dépasser 50 caractères.']),
                ],
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Nom de famille',
                'attr' => ['placeholder' => 'Entrez votre nom de famille'],
                'property_path' => 'nom', // Mappe le champ "last_name" à la propriété "nom" de l'entité
                'constraints' => [
                    new NotBlank(['message' => 'Le nom de famille est obligatoire.']),
                    new Length(['max' => 50, 'maxMessage' => 'Le nom de famille ne peut pas dépasser 50 caractères.']),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => ['placeholder' => 'Entrez une adresse email valide'],
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse email est obligatoire.']),
                    new Email(['message' => 'Veuillez fournir une adresse email valide.']),
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['placeholder' => 'Entrez votre numéro de téléphone'],
                'property_path' => 'telephone', // Mappe le champ "phone" à la propriété "telephone" de l'entité
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone est obligatoire.']),
                    new Length([
                        'min' => 10,
                        'max' => 15,
                        'minMessage' => 'Le numéro de téléphone doit comporter au moins {{ limit }} chiffres.',
                        'maxMessage' => 'Le numéro de téléphone ne peut pas dépasser {{ limit }} chiffres.',
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['placeholder' => 'Entrez votre adresse'],
                'property_path' => 'adresse_utilisateur', // Mappe le champ "address" à la propriété "adresse_utilisateur" de l'entité
                'required' => false,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => ['placeholder' => 'Choisissez un mot de passe sécurisé'],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => ['placeholder' => 'Répétez le mot de passe'],
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'property_path' => 'mot_de_passe', // Maps the form field to the "mot_de_passe" property
                'constraints' => [
                    new NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit comporter au moins {{ limit }} caractères.',
                        'max' => 4096, // Symfony default security limit
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les conditions générales.',
                'mapped' => false,
                'constraints' => [
                    new IsTrue(['message' => 'Vous devez accepter les conditions générales pour continuer.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
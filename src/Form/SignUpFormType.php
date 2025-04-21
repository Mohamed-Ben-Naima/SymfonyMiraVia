<?php

namespace App\Form;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; 
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Utilisateur;

class SignUpFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('prenom', TextType::class, [
            'label' => 'Prénom',
            'attr' => ['placeholder' => 'Entrez votre prénom'],
            'constraints' => [
                new NotBlank(['message' => 'Le prénom est obligatoire.']),
                new Length(['max' => 50, 'maxMessage' => 'Le prénom ne peut pas dépasser 50 caractères.']),
            ],
            'empty_data' => '', // Ensures a string is passed even if the field is empty
        ])
            ->add('nom', TextType::class, [
                'label' => 'Nom de famille',
                'attr' => ['placeholder' => 'Entrez votre nom de famille'],
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
            ->add('telephone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['placeholder' => 'Entrez votre numéro de téléphone'],
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'max' => 15,
                        'minMessage' => 'Le numéro de téléphone doit comporter au moins {{ limit }} chiffres.',
                        'maxMessage' => 'Le numéro de téléphone ne peut pas dépasser {{ limit }} chiffres.',
                    ]),
                ],
            ])
            ->add('adresse_utilisateur', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['placeholder' => 'Entrez votre adresse'],
                'required' => false,
            ])
            /*->add('birthday', DateType::class, [
                'widget' => 'single_text', // Utilise un champ HTML <input type="text">
                'required' => false,       // Rend le champ non obligatoire
                'html5' => false,          // Désactive le widget natif HTML5
                'attr' => [
                    'class' => 'flatpickr', // Classe utilisée pour cibler Flatpickr
                    'placeholder' => 'JJ/MM/AAAA', // Indication pour l'utilisateur
                ],
            ])*/
            ->add('mot_de_passe', RepeatedType::class, [
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
                'constraints' => [
                    new NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit comporter au moins {{ limit }} caractères.',
                        'max' => 4096, // Symfony default security limit
                    ]),
                ],
            ])
            ->add('type_utilisateur', ChoiceType::class, [
                'label' => 'Type d\'utilisateur',
                'choices' => [
                    'Admin' => 'admin',       // Label affiché => valeur stockée
                    'Partenaire' => 'partenaire',
                ],
                'placeholder' => 'Choisissez un type', // Optionnelle : affiche un message par défaut
                'constraints' => [
                    new NotBlank(['message' => 'Le type d\'utilisateur est obligatoire.']),
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
            'data_class' => Utilisateur::class,
        ]);
    }
}
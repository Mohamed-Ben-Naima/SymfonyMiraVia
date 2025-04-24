<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Callback;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Gregwar\CaptchaBundle\Validator\Constraints\Captcha as CaptchaConstraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\EntityManagerInterface;

class SignUpFormType extends AbstractType
{ private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Entrez votre prénom'],
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire.']),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom de famille',
                'attr' => ['placeholder' => 'Entrez votre nom de famille'],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom de famille est obligatoire.']),
                ],
            ])
            ->add('email', TextType::class, [
                'label' => 'Adresse email',
                'attr' => ['placeholder' => 'Entrez votre adresse email'],
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire.']),
                    new Email(['message' => 'Veuillez entrer une adresse email valide.']),
                    new Callback([$this, 'validateEmailUniqueness']),
                ],
            ])
            ->add('captcha', CaptchaType::class, [
        'label' => 'Vérification',
        'mapped' => false, // Ce champ n'est pas lié à l'entité
        'attr' => [
            'placeholder' => 'Entrez le code CAPTCHA',
        ],
        'invalid_message' => 'Le code CAPTCHA est incorrect.',
    ])
            ->add('adresse_utilisateur', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => ['placeholder' => 'Entrez votre adresse'],
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'placeholder' => 'JJ/MM/AAAA',
                    'class' => 'flatpickr',
                ],
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => new \DateTime('today'), // Utilisation de \DateTime pour obtenir la date actuelle
                        'message' => 'La date de naissance ne peut pas être dans le futur.',
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => ['placeholder' => 'Entrez votre numéro de téléphone'],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[0-9]{8,15}$/',
                        'message' => 'Le numéro de téléphone doit contenir entre 8 et 15 chiffres.',
                    ]),
                ],
            ])
            ->add('mot_de_passe', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => ['placeholder' => 'Entrez votre mot de passe'],
                    'constraints' => [
                        new NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                        new Length([
                            'min' => 8,
                            'max' => 255,
                            'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                            'maxMessage' => 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.',
                        ]),
                        new Regex([
                            'pattern' => '/[A-Z]/',
                            'message' => 'Le mot de passe doit contenir au moins une lettre majuscule.',
                        ]),
                        new Regex([
                            'pattern' => '/[0-9]/',
                            'message' => 'Le mot de passe doit contenir au moins un chiffre.',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe',
                    'attr' => ['placeholder' => 'Confirmez votre mot de passe'],
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J’accepte les termes et conditions',
                'required' => true,
            ])
          
            
            ->add('captcha', CaptchaType::class, [
                'label' => 'Vérification',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Entrez le code CAPTCHA',
                ],
                'invalid_message' => 'Le code CAPTCHA est incorrect.',
            ]);
    }
   

public function validateEmailUniqueness($email, ExecutionContextInterface $context): void
{
    $existingUser = $this->entityManager->getRepository(Utilisateurs::class)->findOneBy(['email' => $email]);

    if ($existingUser) {
        $context->buildViolation('Cette adresse email est déjà utilisée.')
            ->addViolation();
    }
}
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
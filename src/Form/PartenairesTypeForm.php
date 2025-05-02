<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints\Count;
use App\Entity\Partenaires;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\File as FileConstraint;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\All;

class PartenairesTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomEntreprise', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir le nom de l\'entreprise.']),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le nom de l\'entreprise ne peut pas dépasser {{ limit }} caractères.',
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Nom de l\'entreprise',
                    'class' => 'form-control',
                    'autocomplete' => 'organization'
                ]
            ])
            ->add('siteWeb', UrlType::class, [
                'label' => 'Site web',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir l\'adresse du site web.']),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'L\'URL ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'https://www.example.com',
                    'class' => 'form-control',
                ]
            ])
            ->add('typeActivite', TextType::class, [
                'label' => 'Type d\'activité',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer le type d\'activité.']),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le type d\'activité ne peut pas dépasser {{ limit }} caractères.',
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Ex : Informatique, Finance, etc.',
                    'class' => 'form-control',
                ]
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse est obligatoire.']),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'L\'adresse ne peut pas dépasser {{ limit }} caractères.',
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Adresse complète de l\'entreprise',
                    'class' => 'form-control',
                ]
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez fournir un numéro de téléphone.']),
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'Le numéro ne peut pas dépasser {{ limit }} chiffres.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Ex : +216 12 345 678',
                    'class' => 'form-control',
                    'pattern' => '\d+',
                    'maxlength' => '15'
                ]
            ])
            ->add('logo', FileType::class, [
                'label' => 'Logo',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez télécharger un logo.']),
                    new FileConstraint([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (jpeg, png, gif)',
                    ])
                ],
            ])
            ->add('autres_documents', FileType::class, [
                'label' => 'Autres documents',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '5M',
                                'mimeTypes' => [
                                    'application/pdf',
                                    'image/jpeg',
                                    'image/png',
                                ],
                                'mimeTypesMessage' => 'Merci de choisir un fichier PDF, JPG ou PNG.',
                            ])
                        ]
                    ])
                ],
                'attr' => [
                    'class' => 'form-control-file',
                    'accept' => '.pdf,.jpg,.jpeg,.png'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Partenaires::class,
        ]);
    }
}

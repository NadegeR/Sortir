<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, ['label' => 'Pseudo'])
            ->add('prenom', TextType::class, ['label' => 'Prenom'])
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('telephone', TextType::class, ['label' => 'Telephone'])
            ->add('email', EmailType::class, ['label'=>'Email'])
            ->add('password', PasswordType::class, ['label'=>'Password'])
            ->add('password' , RepeatedType::class, ['label'=>'Password'])
            ->add('photo', FileType::class, ['label' => 'Photo de profil (.jpg et.png)', 'mapped' => false,
                'constraints' => [new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => ['image/png', 'image/jpeg',],
                    'mimeTypesMessage' => 'Please upload a valid image .jpg .png',
                ])
                ],
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'nom'
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}

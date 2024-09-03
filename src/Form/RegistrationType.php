<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\Adult;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Validator\Constraints as Assert;
class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentYear = (int) date('Y');
        $minYear = $currentYear - 100; 
        $maxYear = $currentYear - 18;

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Prénom']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse courriel',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Adresse courriel']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Mot de passe']
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirmer mot de passe',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Confirmer mot de passe']
            ])
            ->add('status', ChoiceType::class, [
                'attr' => ['class' => 'form-select'],
                'choices'  => [
                    'Client' => 'client',
                    'Vendeur' => 'vendeur',
                    
                ],
                'expanded' => false, // set to true if you want radio buttons instead of a dropdown
                'multiple' => false,
                'label' => 'Vous etes ?',
            ])
            ->add('date_naissance', DateType::class, [
                'label' => 'Date de naissance',
                'attr' => ['class' => 'form-control'],
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'years' => range($maxYear, $minYear),
                'placeholder' => [
                    'year' => 'Annee', 'month' => 'Mois', 'day' => 'Jour'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Adult(),
                ],

            ])
            ->add('captcha', CaptchaType::class, [
                'label' => 'Saisissez ce qui est affiché',
                'attr' => ['class' => 'form-control'],
            ]);
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

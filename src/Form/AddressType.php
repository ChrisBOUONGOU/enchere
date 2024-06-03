<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user']; 
        $builder
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('titre', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('prenom', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('company', TextType::class, [
                'required' => false, 
                'attr' => ['class' => 'form-control']
            ])
            ->add('address', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('postalcode', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('country', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('phone', IntegerType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('city', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choices' => [$user],
                'choice_label' => 'id',
                'attr' => ['class' => 'hidden-field'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'user' => null,
        ]);

        $resolver->setAllowedTypes('user', [User::class, 'null']);
    }
}

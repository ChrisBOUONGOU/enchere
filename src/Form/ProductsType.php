<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom']
            ])
            ->add('starting_price', MoneyType::class ,[
                
                'currency' => 'CAD',
                'label' => 'Prix de départ en',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Prix de départ'],
                'divisor' => 100,

            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Description']
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Télécharger l\'image',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'data_class' => null,
            ])
            ->add('date_creation', null, [
                'label' => 'Date fin d\'enchère',
                'attr' => ['class' => 'form-control'],
                'widget' => 'single_text',

            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Form\DataTransformer\StripTagsTransformer;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\TextEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class StripTagsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new StripTagsTransformer());
    }

    public function getParent()
    {
        return TextEditorType::class;
    }
}

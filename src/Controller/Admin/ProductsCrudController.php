<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\StripTagsType;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class ProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom'),
            MoneyField::new('starting_price')->setCurrency('CAD'),
            TextEditorField::new('description')->setFormType(StripTagsType::class),
            TextField::new('image'),
            DateField::new('date_creation', 'Date de creation')->setFormTypeOption('data', new DateTime()),
        ];
    }
  
}

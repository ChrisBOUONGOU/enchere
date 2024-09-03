<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AdultValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof \DateTime) {
            return;
        }

        $age = $value->diff(new \DateTime())->y;

        if ($age < 18) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ age }}', $age)
                ->addViolation();
        }
    }
}

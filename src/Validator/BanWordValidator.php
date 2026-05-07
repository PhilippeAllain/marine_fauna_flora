<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class BanWordValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var BanWord $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $value = strtolower($value);

        // TODO: implement the validation here
        foreach ($constraint->banWords as $banWord) {
            if (strpos($value, $banWord) !== false) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ banWord }}', $banWord)
                    ->addViolation()
                ;
                break;
            }
        }
    }
}

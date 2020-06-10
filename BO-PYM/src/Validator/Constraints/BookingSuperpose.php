<?php


namespace App\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BookingSuperpose extends Constraint
{
    public $message = "L'événement ne doit pas se chevaucher avec l'évènement '{{ event }}'.";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
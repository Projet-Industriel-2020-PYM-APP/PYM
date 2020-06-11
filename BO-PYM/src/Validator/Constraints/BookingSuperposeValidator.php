<?php

namespace App\Validator\Constraints;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class BookingSuperposeValidator extends ConstraintValidator
{
    private $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }


    public function validate($booking, Constraint $constraint)
    {
        /* @var $constraint BookingSuperpose */


        if (!$booking instanceof Booking) {
            throw new UnexpectedValueException($booking, 'Booking::class');
        }
        $superposeIsAllowed = $booking->getSuperpose();
        $bookingsOfService = $this->bookingRepository->findBy(['service' => $booking->getService()]);
        $bookingsOfService = array_filter($bookingsOfService, function ($bookingOfService) use ($booking) {
            return $bookingOfService->getId() != $booking->getId();
        });

        foreach ($bookingsOfService as $bookingOfService) {
            $overlap = ($bookingOfService->getStartDate() <= $booking->getEndDate()) && ($bookingOfService->getEndDate() >= $booking->getStartDate());

            if ($overlap === true && ($bookingOfService->getSuperpose() === false || $superposeIsAllowed === false)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter("{{ event }}", $bookingOfService->getTitle())
                    ->addViolation();
            }
        }
        return;
    }
}
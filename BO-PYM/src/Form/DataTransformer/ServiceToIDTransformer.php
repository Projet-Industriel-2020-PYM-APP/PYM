<?php


namespace App\Form\DataTransformer;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ServiceToIDTransformer implements DataTransformerInterface
{
    private $repository;

    public function __construct(ServiceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Service $value
     * @return int|null
     */
    public function transform($value)
    {
        return $value !== null ? $value->getId() : null;
    }

    /**
     * @param int $value
     * @return Service|void
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            throw new TransformationFailedException("Le service_id n'est pas définie.");
        }
        $result = $this->repository->find($value);
        if (!$result) {
            $privateErrorMessage = sprintf("Le service '%s' n'existe pas", $value);
            $publicErrorMessage = 'Le service "{{ value }}" n\'existe pas.';
            $failure = new TransformationFailedException($privateErrorMessage);
            $failure->setInvalidMessage($publicErrorMessage, [
                '{{ value }}' => $value,
            ]);
            throw $failure;
        }
        return $result;
    }
}

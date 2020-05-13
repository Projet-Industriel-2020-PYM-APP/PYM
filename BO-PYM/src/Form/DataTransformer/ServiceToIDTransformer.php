<?php


namespace App\Form\DataTransformer;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Symfony\Component\Form\DataTransformerInterface;

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
            return;
        }
        return $this->repository->find($value);
    }
}

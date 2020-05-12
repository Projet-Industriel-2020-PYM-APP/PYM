<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\DataTransformer\ServiceToIDTransformer;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingAPIType extends AbstractType
{
    private $serviceTransformer;
    private $dateTimeTransformer;

    public function __construct(ServiceToIDTransformer $serviceToIDTransformer)
    {
        $this->serviceTransformer = $serviceToIDTransformer;
        $this->dateTimeTransformer =  new DateTimeToStringTransformer(null, null, DateTime::ISO8601);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class)
            ->add('start_date', TextType::class, [
                'property_path' => 'startDate',
            ])
            ->add('end_date', TextType::class, [
                'property_path' => 'endDate',
            ])
        ->add('service_id', TextType::class, [
            'property_path' => 'service',
        ]);

        $builder->get('service_id')->addModelTransformer($this->serviceTransformer);
        $builder->get('start_date')->addModelTransformer($this->dateTimeTransformer);
        $builder->get('end_date')->addModelTransformer($this->dateTimeTransformer);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'csrf_protection' => false,
        ]);
    }
}

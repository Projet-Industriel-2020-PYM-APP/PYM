<?php

namespace App\DataFixtures;

use App\Entity\TypeBatiment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeBatimentFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $entity = new TypeBatiment();
        $entity->setNom('Arret de bus');
        $manager->persist($entity);

        $entity = new TypeBatiment();
        $entity->setNom('PAV');
        $manager->persist($entity);

        $entity = new TypeBatiment();
        $entity->setNom('IRVE');
        $manager->persist($entity);

        $entity = new TypeBatiment();
        $entity->setNom('Forme Paramétrique');
        $manager->persist($entity);

        $manager->flush();
    }
}

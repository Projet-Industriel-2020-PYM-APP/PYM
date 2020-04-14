<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    // ...
    public function load(ObjectManager $manager)
    {
        $user = new Utilisateur();
        $user->setUsername('example@example.com');
        $user->setRole('Admin');
        $user->setEmail('example@example.com');


        $password = $this->encoder->encodePassword($user, '12345678');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

// src/DataFixtures/UserFixtures.php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setFirstName("Prénom$i");
            $user->setLastName("Nom$i");
            $user->setUserName("utilisateur$i");
            $user->setEmailAddress("utilisateur$i@example.com");
            $user->setPassword($this->passwordHasher->hashPassword($user, "password$i"));
            $user->setStatus('actif');
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }
        $manager->flush();
    }
}

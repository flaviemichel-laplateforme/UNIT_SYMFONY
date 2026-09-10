<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    // Injection du service de hachage de mot de passe
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@laplateforme.io');

        // Hachage du mot de passe "azerty"
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'azerty');
        $user->setPassword($hashedPassword);

        // Préparation et envoi à la base de données
        $manager->persist($user);
        $manager->flush();
    }
}

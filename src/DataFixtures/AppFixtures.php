<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Langues disponibles
        $languages = ['Anglais', 'Espagnol', 'Italien', 'Arabe'];

        // Niveaux
        $levels = ['A1', 'A2', 'B1', 'B2'];

        $commonDuration = 80;
        $commonPrice = 350;

        foreach ($languages as $language) {
            foreach ($levels as $level) {
                $course = new Course();
                $course->setName("$language $level");
                $course->setLevel($level);
                $course->setDuration($commonDuration);
                $course->setDescription("Cours de $language niveau $level");
                $course->setPrice($commonPrice);
                $manager->persist($course);
            }
        }

        // Création d'un utilisateur
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setFirstName('Jean');
        $user->setLastName('Dupont');
        $user->setPhoneNumber('0102030405');
        $user->setCreatedAt(new \DateTime());
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        $manager->flush();
    }
}

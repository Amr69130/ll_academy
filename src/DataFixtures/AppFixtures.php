<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Enrollment;
use App\Entity\EnrollmentPeriod;
use App\Entity\Payment;
use App\Entity\PaymentType;
use App\Entity\User;
use App\Entity\Student;
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
        // ---------- CREATION DES COURS ----------
        $languages = ['anglais', 'espagnol', 'italien', 'arabe'];
        $levels = ['A1', 'A2', 'B1', 'B2'];

        // Prix par niveau (en euros) indicatif pour 1 an de cours
        $priceByLevel = [
            'A1' => 300,
            'A2' => 350,
            'B1' => 400,
            'B2' => 450,
        ];

        $courses = [];

        foreach ($languages as $language) {
            foreach ($levels as $level) {
                $course = new Course();
                $course->setName(ucfirst($language) . " " . $level);
                $course->setLevel($level);
                $course->setDescription("Cours de " . ucfirst($language) . " niveau " . $level);
                $course->setPrice($priceByLevel[$level]);  // Prix par niveau
                $course->setFlagPicture($language . '.png');
                $course->setIsOpen(true);
                $manager->persist($course);

                $courses[] = $course;
            }
        }

        // ---------- CREATION DES PAYMENT TYPES ----------
        $paymentTypes = [];
        foreach (['Espèces', 'Virement', 'Carte bancaire'] as $name) {
            $paymentType = new PaymentType();
            $paymentType->setName($name);
            $manager->persist($paymentType);
            $paymentTypes[] = $paymentType;
        }

        // ---------- USERS TEST ----------
        $userTest = new User();
        $userTest->setEmail('usertest@example.com');
        $userTest->setFirstName('User');
        $userTest->setLastName('Test');
        $userTest->setPhoneNumber('0101010101');
        $userTest->setCreatedAt(new \DateTime());
        $userTest->setRoles(['ROLE_USER']);
        $userTest->setPassword($this->passwordHasher->hashPassword($userTest, 'usertest'));
        $manager->persist($userTest);

        $adminTest = new User();
        $adminTest->setEmail('admintest@example.com');
        $adminTest->setFirstName('Admin');
        $adminTest->setLastName('Test');
        $adminTest->setPhoneNumber('0202020202');
        $adminTest->setCreatedAt(new \DateTime());
        $adminTest->setRoles(['ROLE_ADMIN']);
        $adminTest->setPassword($this->passwordHasher->hashPassword($adminTest, 'admintest'));
        $manager->persist($adminTest);

        // ---------- CREATION DES PARENTS ----------
        $parentFirstNames = ['Marie', 'Jean', 'Claire', 'Michel', 'Sophie', 'Pierre', 'Isabelle', 'François', 'Élodie', 'Laurent'];
        $parentLastNames = ['Dupont', 'Martin', 'Leroy', 'Moreau', 'Petit', 'Rousseau', 'Faure', 'Blanc', 'Garnier', 'Chevalier'];

        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail(strtolower($parentFirstNames[$i]) . "." . strtolower($parentLastNames[$i]) . "@example.com");
            $user->setFirstName($parentFirstNames[$i]);
            $user->setLastName($parentLastNames[$i]);
            $user->setPhoneNumber('06' . rand(10000000, 99999999));
            $user->setCreatedAt(new \DateTime());
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));

            $manager->persist($user);
            $users[] = $user;
        }

        // ---------- CREATION DES ELEVES ----------
        $studentData = [
            ['prenom' => 'Lucas', 'nom' => 'Dupont', 'parentIndex' => 0],
            ['prenom' => 'Emma', 'nom' => 'Dupont', 'parentIndex' => 0],
            ['prenom' => 'Maxime', 'nom' => 'Martin', 'parentIndex' => 1],
            ['prenom' => 'Chloé', 'nom' => 'Martin', 'parentIndex' => 1],
            ['prenom' => 'Nathan', 'nom' => 'Leroy', 'parentIndex' => 2],
            ['prenom' => 'Léa', 'nom' => 'Leroy', 'parentIndex' => 2],
            ['prenom' => 'Camille', 'nom' => 'Moreau', 'parentIndex' => 3],
            ['prenom' => 'Jules', 'nom' => 'Moreau', 'parentIndex' => 3],
            ['prenom' => 'Juliette', 'nom' => 'Petit', 'parentIndex' => 4],
            ['prenom' => 'Thomas', 'nom' => 'Petit', 'parentIndex' => 4],
            ['prenom' => 'Manon', 'nom' => 'Rousseau', 'parentIndex' => 5],
            ['prenom' => 'Arthur', 'nom' => 'Rousseau', 'parentIndex' => 5],
            ['prenom' => 'Léna', 'nom' => 'Faure', 'parentIndex' => 6],
            ['prenom' => 'Louis', 'nom' => 'Faure', 'parentIndex' => 6],
            ['prenom' => 'Anaïs', 'nom' => 'Blanc', 'parentIndex' => 7],
        ];

        $fakeAddresses = [
            ['adress' => '10 rue de Paris', 'city' => 'Paris', 'zipCode' => '75001'],
            ['adress' => '15 avenue de Lyon', 'city' => 'Lyon', 'zipCode' => '69002'],
            ['adress' => '7 boulevard Marseille', 'city' => 'Marseille', 'zipCode' => '13003'],
            ['adress' => '22 place Nantes', 'city' => 'Nantes', 'zipCode' => '44000'],
            ['adress' => '5 rue Lille', 'city' => 'Lille', 'zipCode' => '59000'],
        ];

        $students = [];
        foreach ($studentData as $data) {
            $student = new Student();
            $student->setFirstName($data['prenom']);
            $student->setLastName($data['nom']);

            $address = $fakeAddresses[array_rand($fakeAddresses)];
            $student->setAdress($address['adress']);
            $student->setCity($address['city']);
            $student->setZipCode($address['zipCode']);

            $student->setBirthDate(new \DateTime(rand(2005, 2018) . '-' . rand(1, 12) . '-' . rand(1, 28)));

            $student->setUser($users[$data['parentIndex']]);

            $manager->persist($student);
            $students[] = $student;
        }

        // ---------- CREATION DES PERIODES ----------
        $period2024 = new EnrollmentPeriod();
        $period2024->setTitle('2024');
        $period2024->setIsOpen(false);
        $manager->persist($period2024);

        $period2025 = new EnrollmentPeriod();
        $period2025->setTitle('2025');
        $period2025->setIsOpen(true);
        $manager->persist($period2025);

        $periods = [$period2024, $period2025];

        // ---------- CREATION DES ENROLLMENTS + PAYMENTS ----------
        foreach ($students as $student) {
            $enrollment = new Enrollment();
            $enrollment->setStudent($student);
            $enrollment->setEnrollmentDate(new \DateTime());
            $enrollment->setStatus(rand(0, 1) ? 'Validé' : 'En attente');
            $enrollment->setCourse($courses[array_rand($courses)]);
            $enrollment->setEnrollmentPeriod($periods[array_rand($periods)]);

            $manager->persist($enrollment);

            $paymentCount = rand(1, 2);
            for ($p = 1; $p <= $paymentCount; $p++) {
                $payment = new Payment();
                $payment->setEnrollment($enrollment);
                $payment->setPaymentDate(new \DateTime('-' . rand(1, 90) . ' days'));
                $payment->setAmount($enrollment->getCourse()->getPrice() / $paymentCount);
                $payment->setStatus(rand(0, 1) ? 'Payé' : 'En attente');
                $payment->setTransactionRef('TX-' . strtoupper(bin2hex(random_bytes(4))));
                $payment->setPaymentType($paymentTypes[array_rand($paymentTypes)]);

                $manager->persist($payment);
            }
        }

        $manager->flush();
    }
}

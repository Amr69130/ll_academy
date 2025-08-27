<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Enrollment;
use App\Entity\EnrollmentPeriod;
use App\Entity\Payment;
use App\Entity\PaymentType;
use App\Entity\Post;
use App\Entity\PostType;
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

        $languages = [
            'anglais' => [
                'A1' => 'Découvrez les bases de l’anglais à travers des exercices simples et des conversations guidées. Idéal pour les débutants.',
                'A2' => 'Renforcez vos connaissances en anglais avec des dialogues plus complexes et un vocabulaire élargi.',
                'B1' => 'Approfondissez votre anglais avec des conversations avancées, lecture de textes authentiques et préparation professionnelle.',
                'B2' => 'Perfectionnez votre anglais à un niveau avancé, avec grammaire fine et communication fluide.'
            ],
            'espagnol' => [
                'A1' => 'Initiez-vous à l’espagnol en découvrant le vocabulaire essentiel et les expressions de base.',
                'A2' => 'Consolidez vos compétences avec des dialogues simples et des lectures faciles.',
                'B1' => 'Maîtrisez des conversations complexes et améliorez votre compréhension écrite et orale.',
                'B2' => 'Perfectionnez votre espagnol avec un focus sur la grammaire subtile et l’expression fluide.'
            ],
            'italien' => [
                'A1' => 'Apprenez les bases de l’italien grâce à des exercices interactifs et des situations de la vie quotidienne.',
                'A2' => 'Renforcez votre italien avec des dialogues plus structurés et la découverte de la culture italienne.',
                'B1' => 'Progressez vers un niveau intermédiaire avec des textes authentiques et des conversations guidées.',
                'B2' => 'Atteignez un niveau avancé avec fluidité, grammaire avancée et compréhension culturelle.'
            ],
            'arabe' => [
                'A1' => 'Introduction à l’arabe moderne avec apprentissage de l’alphabet et phrases simples pour communiquer rapidement.',
                'A2' => 'Approfondissez vos bases avec des dialogues simples, lecture de textes courts et exercices de prononciation.',
                'B1' => 'Développez votre arabe avec des conversations intermédiaires et compréhension orale et écrite.',
                'B2' => 'Perfectionnez votre arabe pour atteindre un niveau avancé, expression écrite et orale fluide.'
            ]
        ];

        $priceByLevel = [
            'A1' => 300,
            'A2' => 350,
            'B1' => 400,
            'B2' => 450,
        ];

        $courses = [];

        foreach ($languages as $language => $levelsDescriptions) {
            foreach ($levelsDescriptions as $level => $description) {
                $course = new Course();
                $course->setName(ucfirst($language) . " " . $level);
                $course->setLevel($level);
                $course->setDescription($description);
                $course->setPrice($priceByLevel[$level]);
                $course->setFlagPicture('courses/' . $language . '.png');

                // alterner le statut ouvert / fermé
                $isOpen = rand(0, 1) === 1;
                $course->setIsOpen($isOpen);

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
            ['address' => '10 rue de Paris', 'city' => 'Paris', 'zipCode' => '75001'],
            ['address' => '15 avenue de Lyon', 'city' => 'Lyon', 'zipCode' => '69002'],
            ['address' => '7 boulevard Marseille', 'city' => 'Marseille', 'zipCode' => '13003'],
            ['address' => '22 place Nantes', 'city' => 'Nantes', 'zipCode' => '44000'],
            ['address' => '5 rue Lille', 'city' => 'Lille', 'zipCode' => '59000'],
        ];

        $students = [];
        foreach ($studentData as $data) {
            $student = new Student();
            $student->setFirstName($data['prenom']);
            $student->setLastName($data['nom']);
            $address = $fakeAddresses[array_rand($fakeAddresses)];
            $student->setAddress($address['address']);
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

        $enrollmentStatuses = ['pending', 'rejected', 'approved'];
        $paymentStatuses = ['pending', 'approved'];

        // ---------- CREATION DES ENROLLMENTS + PAYMENTS ----------
        foreach ($students as $student) {
            $random_index = array_rand($courses);
            $random_course = $courses[$random_index];
            $nextLevelCourse = ($random_index < count($courses) - 1) ? $courses[$random_index + 1] : $courses[0];

            // Inscription 2024
            $enrollment1 = new Enrollment();
            $enrollment1->setStudent($student);
            $enrollment1->setEnrollmentDate(new \DateTime('2024-01-10'));
            $enrollment1->setStatus($enrollmentStatuses[array_rand($enrollmentStatuses)]);
            $enrollment1->setCourse($random_course);
            $enrollment1->setEnrollmentPeriod($period2024);
            $manager->persist($enrollment1);

            $paymentCount = rand(1, 2);
            for ($p = 1; $p <= $paymentCount; $p++) {
                $payment = new Payment();
                $payment->setEnrollment($enrollment1);
                $payment->setPaymentDate(new \DateTime('2024-02-0' . $p));
                $payment->setAmount($enrollment1->getCourse()->getPrice() / $paymentCount);
                $payment->setStatus($paymentStatuses[array_rand($paymentStatuses)]);
                $payment->setTransactionRef('TX-' . strtoupper(bin2hex(random_bytes(4))));
                $payment->setPaymentType($paymentTypes[array_rand($paymentTypes)]);
                $manager->persist($payment);
            }

            // Inscription 2025
            $enrollment2 = new Enrollment();
            $enrollment2->setStudent($student);
            $enrollment2->setEnrollmentDate(new \DateTime('2025-01-10'));
            $enrollment2->setStatus($enrollmentStatuses[array_rand($enrollmentStatuses)]);
            $enrollment2->setCourse($nextLevelCourse);
            $enrollment2->setEnrollmentPeriod($period2025);
            $manager->persist($enrollment2);

            $paymentCount = rand(1, 2);
            for ($p = 1; $p <= $paymentCount; $p++) {
                $payment = new Payment();
                $payment->setEnrollment($enrollment2);
                $payment->setPaymentDate(new \DateTime('2025-02-0' . $p));
                $payment->setAmount($enrollment2->getCourse()->getPrice() / $paymentCount);
                $payment->setStatus($paymentStatuses[array_rand($paymentStatuses)]);
                $payment->setTransactionRef('TX-' . strtoupper(bin2hex(random_bytes(4))));
                $payment->setPaymentType($paymentTypes[array_rand($paymentTypes)]);
                $manager->persist($payment);
            }
        }
        // Création des types de post
        $typeActualite = new PostType();
        $typeActualite->setType('Actualité');
        $manager->persist($typeActualite);

        $typeFaq = new PostType();
        $typeFaq->setType('FAQ');
        $manager->persist($typeFaq);


        $staff = new PostType();
        $staff->setType('Staff');
        $manager->persist($staff);

        // -------- POSTS ACTUALITÉS --------
        $post1 = new Post();
        $post1->setTitle('Lancement du nouveau site');
        $post1->setContent('Notre nouveau site est enfin en ligne ! Explorez nos cours et nos nouveautés.');
        $post1->setImage('site_launch.jpg');
        $post1->setType($typeActualite);
        $post1->setCreatedAt(new \DateTime());
        $manager->persist($post1);

        $post2 = new Post();
        $post2->setTitle('Rentrée 2025 : nouvelles classes');
        $post2->setContent('De nouveaux créneaux sont disponibles pour les débutants en anglais et espagnol.');
        $post2->setImage('rentree2025.jpg');
        $post2->setType($typeActualite);
        $post2->setCreatedAt(new \DateTime());
        $manager->persist($post2);

        // === Professeurs d’anglais ===
        $prof1 = new Post();
        $prof1->setType($staff);
        $prof1->setTitle('Emma Thompson');
        $prof1->setContent('Professeure d’anglais passionnée, spécialisée dans l’oral et les échanges culturels.');
        $prof1->setImage('professors/emma_thompson.jpg');
        $prof1->setCreatedAt(new \DateTime());
        $manager->persist($prof1);

        $prof2 = new Post();
        $prof2->setType($staff);
        $prof2->setTitle('James Carter');
        $prof2->setContent('Expert en grammaire anglaise et préparation aux certifications TOEIC et IELTS.');
        $prof2->setImage('professors/james_carter.jpg');
        $prof2->setCreatedAt(new \DateTime());
        $manager->persist($prof2);

        // === Professeurs d’arabe ===
        $prof3 = new Post();
        $prof3->setType($staff);
        $prof3->setTitle('Layla Al Fahad');
        $prof3->setContent('Spécialiste de l’arabe littéraire et des dialectes du Maghreb.');
        $prof3->setImage('professors/layla_al_fahad.jpg');
        $prof3->setCreatedAt(new \DateTime());
        $manager->persist($prof3);

        $prof4 = new Post();
        $prof4->setType($staff);
        $prof4->setTitle('Omar Hassan');
        $prof4->setContent('Enseignant expérimenté en arabe moderne, passionné de poésie classique.');
        $prof4->setImage('professors/omar_hassan.jpg');
        $prof4->setCreatedAt(new \DateTime());
        $manager->persist($prof4);

        // === Professeurs d’espagnol ===
        $prof5 = new Post();
        $prof5->setType($staff);
        $prof5->setTitle('Maria Gonzales');
        $prof5->setContent('Née à Madrid, elle enseigne l’espagnol depuis 10 ans avec une approche ludique.');
        $prof5->setImage('professors/maria_gonzales.jpg');
        $prof5->setCreatedAt(new \DateTime());
        $manager->persist($prof5);

        $prof6 = new Post();
        $prof6->setType($staff);
        $prof6->setTitle('Carlos Ruiz');
        $prof6->setContent('Professeur d’espagnol spécialisé dans les affaires et la communication professionnelle.');
        $prof6->setImage('professors/carlos_ruiz.jpg');
        $prof6->setCreatedAt(new \DateTime());
        $manager->persist($prof6);

        // === Professeurs d’italien ===
        $prof7 = new Post();
        $prof7->setType($staff);
        $prof7->setTitle('Giulia Morettii');
        $prof7->setContent('Italienne native, elle enseigne la langue et la culture à travers la cuisine et la musique.');
        $prof7->setImage('professors/giulia_moretti.jpg');
        $prof7->setCreatedAt(new \DateTime());
        $manager->persist($prof7);

        $prof8 = new Post();
        $prof8->setType($staff);
        $prof8->setTitle('Luca Bianchi');
        $prof8->setContent('Professeur dynamique d’italien, adepte de méthodes immersives et interactives.');
        $prof8->setImage('professors/luca_bianchi.jpg');
        $prof8->setCreatedAt(new \DateTime());
        $manager->persist($prof8);

        // -------- POSTS FAQ --------
        $faq1 = new Post();
        $faq1->setTitle('Comment s\'inscrire à un cours ?');
        $faq1->setContent('Rendez-vous sur la page Inscriptions et suivez les étapes indiquées.');
        $faq1->setImage(null);
        $faq1->setType($typeFaq);
        $faq1->setCreatedAt(new \DateTime());
        $manager->persist($faq1);

        $faq2 = new Post();
        $faq2->setTitle('Peut-on payer en plusieurs fois ?');
        $faq2->setContent('Oui, le paiement en 2 ou 3 fois est possible par carte bancaire.');
        $faq2->setImage(null);
        $faq2->setType($typeFaq);
        $faq2->setCreatedAt(new \DateTime());
        $manager->persist($faq2);

        $manager->flush();
    }
}
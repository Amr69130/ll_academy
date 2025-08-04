<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Schedule;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //4 langues disponibles dans l'école ll_academy
        $languages = ['Anglais', 'Espagnol', 'Italien', 'Arabe'];

        //4 niveaux pour chaque langues
        $levels = ['A1', 'A2', 'B1', 'B2'];

        //tous on le même nombre d'heures réparties dans l'année donc valeur commune à tous
        $commonDuration = 80;

        //tous ont le même prix
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
        $manager->flush();
    }

}

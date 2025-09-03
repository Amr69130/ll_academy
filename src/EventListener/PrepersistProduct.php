<?php

namespace App\EventListener;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class PrepersistProduct
{
    #[AsEntityListener(event: Events::postUpdate, method: 'addProduct', entity: Course::class)]
    public function addProduct(Course $course, PostUpdateEventArgs $event)
    {

    }
}
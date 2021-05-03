<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i = 1; $i <= 20; $i++) {
            $image = new Image();
            $image->setId($i);
            $image->setPath("img$i.png");
            $manager->persist($image);
        }
        $manager->flush();
    }
}

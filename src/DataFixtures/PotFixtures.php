<?php

namespace App\DataFixtures;

use App\Factory\PostFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PotFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        PostFactory::createMany(5);
    }
}

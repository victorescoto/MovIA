<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 100; $i++) {
            $movie = new Movie();
            $movie->setTitle($faker->sentence);
            $movie->setYear($faker->year);
            $movie->setImdbId($faker->regexify('tt[0-9]{7}'));
            $movie->setType('movie');
            $movie->setPoster($faker->imageUrl);

            $manager->persist($movie);
        }

        $manager->flush();
    }
}

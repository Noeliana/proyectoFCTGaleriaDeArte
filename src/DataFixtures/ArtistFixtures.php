<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArtistFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $artists = [
            'George Lucas',
            'George Lucas',
            'Walt Disney',
            'Stan Lee',
            'Marina Ortega',
            'Carlos Gómez',
            'Elena Sánchez',
            'Tomás Vidal'
        ];

        $counter = 1;
        foreach ($artists as $name) {
            $artist = new Artist();
            $artist->setName($name);
            $artist->setBio("Biografía de $name");
            $artist->setWebsite("https://example.com/$counter");
            $artist->setImage("$counter.jpeg");

            $manager->persist($artist);
            $counter++;
        }

        $manager->flush();
    }
}

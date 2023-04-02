<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Organisation;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $organisation = new Organisation;
            $organisation->setName('Organisation ' . $i);
            $manager->persist($organisation);

            $building = new Building;
            $building->setName('Building' . $i);
            $building->setZipcode(59000 + $i);
            $manager->persist($building);

            $room = new Room;
            $room->setName('Room' . $i);
            $room->setNumberOfPeople(rand(1, 20));
            $manager->persist($room);
        }

        $manager->flush();
    }
}

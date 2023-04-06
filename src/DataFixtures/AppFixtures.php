<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Organisation;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Création de 10 organisations
        for ($i = 0; $i < 10; $i++) {
            $organization = new Organisation();
            $organization->setName($faker->company());
            $manager->persist($organization);

            // Création de 5 bâtiments pour chaque organisation
            for ($j = 0; $j < 5; $j++) {
                $building = new Building();
                $building->setName($faker->company());
                $building->setZipcode($faker->postcode());
                $building->addOrganisation($organization);
                $manager->persist($building);

                // Création de 10 pièces pour chaque bâtiment
                for ($k = 0; $k < 10; $k++) {
                    $room = new Room();
                    $room->setName($faker->word());
                    $room->setNumberOfPeople($faker->numberBetween(0, 20));
                    $room->setBuilding($building);
                    $manager->persist($room);
                }
            }
        }

        $manager->flush();
    }
}

<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Courier;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadCouriersData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('ru_RU');
        $couriers = [];
        for ($i = 0; $i < 15; $i++) {
            $courier = new Courier();
            $courier->setFullName($faker->name);
            $manager->persist($courier);
            $couriers[] = $courier;
        }
        $manager->flush();
        for ($i = 0, $couriersCount = count($couriers); $i < $couriersCount; $i++) {
            $this->addReference('courier_' . $i, $couriers[$i]);
        }
    }

    public function getOrder()
    {
        return 2;
    }
}
<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Region;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRegionsData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $regionsNames = [
            'Санкт-Петербург',
            'Уфа',
            'Нижний Новгород',
            'Владимир',
            'Кострома',
            'Екатеринбург',
            'Ковров',
            'Воронеж',
            'Самара',
            'Астрахань',
        ];

        $regions = [];
        foreach ($regionsNames as $regionName) {
            $randomTravelTime = rand(1, 15);
            $region = new Region();
            $region->setName($regionName)
                ->setTravelTime($randomTravelTime);
            $manager->persist($region);
            $regions[] = $region;
        }
        $manager->flush();

        for ($i = 0, $regionsCount = count($regions); $i < $regionsCount; $i++) {
            $this->addReference('region_' . $i, $regions[$i]);
        }
    }

    public function getOrder()
    {
        return 1;
    }
}
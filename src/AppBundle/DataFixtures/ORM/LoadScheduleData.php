<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Courier;
use AppBundle\Entity\Region;
use AppBundle\Entity\Schedule;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadScheduleData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $startDate = new \DateTime('2015-06-01');
        $endDate = new \DateTime('now');

        // генерируем записи в массив
        $scheduleList = [];
        for ($i = 0; $i < 15; $i++) {
            /** @var Courier $courier */
            $courier = $this->getReference('courier_' . $i);
            $departureDate = clone $startDate;
            while ($departureDate <= $endDate) {
                if (!rand(0, 1)) {
                    $departureDate = $departureDate->add(new \DateInterval('P1D'));
                    continue;
                }
                $schedule = new Schedule();
                /** @var Region $randomRegion */
                $randomRegion = $this->getReference('region_' . rand(0, 9));
                $schedule->setCourier($courier)
                    ->setRegion($randomRegion)
                    ->setDepartureDate(clone $departureDate);

                $travelTimeOneDirection = $randomRegion->getTravelTime();
                $travelTimeTwoDirections = new \DateInterval('P' . 2 * $travelTimeOneDirection . 'D');
                $departureDate = $departureDate->add($travelTimeTwoDirections)->add(new \DateInterval('P1D'));

                $scheduleList[] = $schedule;
            }
        }

        // сортируем записи по дате отъезда
        usort($scheduleList, function ($scheduleA, $scheduleB) {
            /**
             * @var Schedule $scheduleA
             * @var Schedule $scheduleB
             */
            return ($scheduleA->getDepartureDate()->getTimestamp() - $scheduleB->getDepartureDate()->getTimestamp());
        });

        // заносим записи в БД
        foreach ($scheduleList as $schedule){
            $manager->persist($schedule);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
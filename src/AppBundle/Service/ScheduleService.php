<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

class ScheduleService
{

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param int $courierId
     * @return array
     */
    public function getCourierBusyDates($courierId)
    {
        $courierData = $this->em->getRepository('AppBundle:Schedule')->getCourierTravelDates($courierId);
        $courierBusyDays = [];
        foreach ($courierData as $data) {
            $begin = $data['departureDate'];
            $end = $data['arrivalDate'];
            $dateRange = new \DatePeriod($begin, new \DateInterval('P1D'), $end);
            /** @var \DateTime $date */
            foreach ($dateRange as $date) {
                array_push($courierBusyDays, $date->format("Y-m-d"));
            }
        }

        return $courierBusyDays;
    }
}
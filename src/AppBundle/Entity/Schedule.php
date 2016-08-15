<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ScheduleRepository")
 * @ORM\Table(name="schedule")
 * @ORM\HasLifecycleCallbacks()
 */
class Schedule
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="departure_date", type="date")
     */
    protected $departureDate;

    /**
     * @ORM\Column(name="arrival_date", type="date")
     */
    protected $arrivalDate;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Region", inversedBy="schedule")
     * @ORM\JoinColumn(name="region__id")
     */
    protected $region;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Courier", inversedBy="schedule")
     * @ORM\JoinColumn(name="courier__id")
     */
    protected $courier;

    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        $departureDate = clone $this->getDepartureDate();
        $travelTimeOneDirection = $this->getRegion()->getTravelTime();
        $travelTimeTwoDirections = new \DateInterval('P' . 2 * $travelTimeOneDirection . 'D');
        $this->arrivalDate = $departureDate->add($travelTimeTwoDirections);
    }

    /**
     * Get departureDate
     *
     * @return \DateTime
     */
    public function getDepartureDate()
    {
        return $this->departureDate;
    }

    /**
     * Set departureDate
     *
     * @param \DateTime $departureDate
     * @return Schedule
     */
    public function setDepartureDate($departureDate)
    {
        $this->departureDate = $departureDate;

        return $this;
    }

    /**
     * Get region
     *
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set region
     *
     * @param Region $region
     * @return Schedule
     */
    public function setRegion(Region $region = NULL)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get arrivalDate
     *
     * @return \DateTime
     */
    public function getArrivalDate()
    {
        return $this->arrivalDate;
    }

    /**
     * Set arrivalDate
     *
     * @param \DateTime $arrivalDate
     * @return Schedule
     */
    public function setArrivalDate($arrivalDate)
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }

    /**
     * Get courier
     *
     * @return Courier
     */
    public function getCourier()
    {
        return $this->courier;
    }

    /**
     * Set courier
     *
     * @param Courier $courier
     * @return Schedule
     */
    public function setCourier(Courier $courier = NULL)
    {
        $this->courier = $courier;

        return $this;
    }
}

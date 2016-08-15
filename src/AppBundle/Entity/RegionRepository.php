<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * RegionRepository
 */
class RegionRepository extends EntityRepository
{
    /**
     * @param int $regionId
     * @return int
     */
    public function getRegionTravelTime($regionId)
    {
        return (int) $this->createQueryBuilder('r')
            ->select('r.travelTime')
            ->where('r.id = :regionId')
            ->setParameter('regionId', $regionId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}

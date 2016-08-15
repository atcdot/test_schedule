<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ScheduleRepository
 */
class ScheduleRepository extends EntityRepository
{
    /**
     * @param int $courierId
     * @return array
     */
    public function getCourierTravelDates($courierId)
    {
        return $this->createQueryBuilder('s')
            ->select(['s.departureDate, s.arrivalDate'])
            ->innerJoin('s.courier', 'c')
            ->where('c.id = :courierId')
            ->setParameter('courierId', $courierId)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param int   $start
     * @param int   $length
     * @param array $columnsParams
     * @param array $order
     * @param array $extraSearch
     * @return array
     */
    public function getFilteredScheduleList($start, $length, $columnsParams, $order, $extraSearch)
    {
        $data = $this->createQueryBuilder('s')
            ->select(['s.id AS id, r.name AS region, s.departureDate AS departureDate, c.fullName AS courier'])
            ->leftJoin('s.courier', 'c')
            ->leftJoin('s.region', 'r')
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->orderBy($columnsParams[$order['column']]['data'], $order['dir']);
        if (!empty($extraSearch['dateFrom'])) {
            $data->andWhere('s.departureDate >= :dateFrom')
                ->setParameter('dateFrom', $extraSearch['dateFrom']);
        }
        if (!empty($extraSearch['dateTo'])) {
            $data->andWhere('s.departureDate <= :dateTo')
                ->setParameter('dateTo', $extraSearch['dateTo']);
        }
        $data = $data->getQuery()
            ->getArrayResult();

        $data = array_map(function ($dataItem) {
            /** @var \DateTime $departureDate */
            $departureDate = $dataItem['departureDate'];
            $dataItem['departureDate'] = $departureDate->format('Y-m-d');

            return $dataItem;
        }, $data);

        return $data;
    }

    /**
     * @return mixed
     */
    public function getScheduleListCount()
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param array $filter
     * @return mixed
     */
    public function getFilteredScheduleListCount($filter)
    {
        $count = $this->createQueryBuilder('s')
            ->select('COUNT(s)');
        if (!empty($filter['dateFrom'])) {
            $count->andWhere('s.departureDate >= :dateFrom')
                ->setParameter('dateFrom', $filter['dateFrom']);
        }
        if (!empty($extraSearch['dateTo'])) {
            $count->andWhere('s.departureDate <= :dateTo')
                ->setParameter('dateTo', $filter['dateTo']);
        }
        $count = $count->getQuery()
            ->getSingleScalarResult();

        return $count;
    }


}

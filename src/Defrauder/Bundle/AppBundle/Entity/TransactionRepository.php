<?php

namespace Defrauder\Bundle\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TransactionRepository extends EntityRepository
{
    public function getAllZips()
    {
        $em = $this->getEntityManager();

        $result = $em->createQuery('SELECT tra.zip FROM AppBundle:Transaction tra')->getScalarResult();

        $zips = array_map('current', $result);

        return $zips;
    }

    public function getAvgAmount()
    {
        $em = $this->getEntityManager();

        $avg = $em->createQuery('SELECT AVG(tra.amount) FROM AppBundle:Transaction tra')->getOneOrNullResult();

        if (empty($avg)) {
            return 0;
        } else {
            return round($avg[1], 2);
        }
    }
}
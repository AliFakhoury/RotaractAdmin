<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/9/2018
 * Time: 12:02 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class positionsRepository extends EntityRepository{
    public function findPositions(){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from('AppBundle:Positions','p');

        $qb = $qb->getQuery()->execute();

        return $qb;
    }
}
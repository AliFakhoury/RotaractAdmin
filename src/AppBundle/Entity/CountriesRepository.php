<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/17/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class CountriesRepository extends EntityRepository{
    public function findAllCountries(){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Country', 'c');

        $qb = $qb->getQuery()->execute();

        return $qb;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/17/2018
 * Time: 6:07 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class messagesRepository extends EntityRepository{
    public function findAllByPage($perPage, $pageNumber){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('m')
            ->from('AppBundle:Message', 'm')
            ->setFirstResult($perPage*($pageNumber-1))
            ->setMaxResults($perPage);

        $qb = $qb->getQuery()->execute();

        return $qb;
    }

    public function countPages($perPage){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(m)')
            ->from('AppBundle:Message', 'm');

        $qb = $qb->getQuery()->execute();
        $count = ceil($qb[0][1]/$perPage);

        return $count;
    }

    public function getMessageById($id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT *
            FROM messages
            WHERE messages.message_id = '.$id;

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $message = $stmt->fetch();

        return $message;
    }
}
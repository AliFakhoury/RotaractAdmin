<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/11/2018
 * Time: 4:41 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class membersRepository extends EntityRepository {
    public function findMembersByProjectId($id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT users.first_name, users.last_name
            FROM project_members
            JOIN users
              ON users.user_id = project_members.user_id
            JOIN projects
              ON projects.project_id = project_members.project_id
              WHERE projects.project_id = '.$id;

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $members = $stmt->fetchAll();

        return $members;
    }

    public function findMembersIdByProjectId($id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT user_id
                FROM project_members
                WHERE project_id = '.$id;

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $members = $stmt->fetchAll(7);

        return $members;
    }

    public function removeMembersForProject($id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'DELETE 
                FROM project_members
                WHERE project_id = '.$id;

        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }
}
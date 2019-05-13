<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/11/2018
 * Time: 4:41 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class projectsRepository extends EntityRepository {
    public function findProjectsByPage($pageNumber, $perPage){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from('AppBundle:Projects', 'p')
            ->andWhere('p.is_deleted = 0')
            ->setFirstResult($perPage*($pageNumber-1))
            ->setMaxResults($perPage);

        $qb = $qb->getQuery()->execute();

        $conn = $this->getEntityManager()->getConnection();
        $projects = array();

        for($i = 0; $i < sizeof($qb); $i++){
            $sql ='SELECT projects.project_id, projects.project_name, projects.description, projects.date_of_project, users.first_name, users.last_name
            FROM projects
            JOIN users
              ON users.user_id = '.$qb[$i]->getHeadOfProject().' AND projects.project_id = '.$qb[$i]->getProjectID();

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $project = $stmt->fetch();
            dump($project);
            if(!empty($project)){
                array_push($projects, $project);
            }
        }

        return $projects;
    }

    public function findProjectByIdView($id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT projects.project_id, projects.project_name, projects.description, projects.date_of_project, users.first_name, users.last_name
            FROM projects
            JOIN users
              ON users.user_id = projects.head_of_project
            WHERE projects.project_id = '.$id;

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $project = $stmt->fetch();

        return $project;
    }

    public function findProjectById($id){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from('AppBundle:Projects', 'p')
            ->andWhere('p.project_ID = '.$id);

        $qb = $qb->getQuery()->execute();

        return $qb;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 24/11/2017
 * Time: 11:09 AM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;
use PDO;

class adminRepository extends EntityRepository {
    public function findAllCriteria($data, $pageNumber, $perPage){
        $admins = array();

        $conn = $this->getEntityManager()->getConnection();

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('AppBundle:admin', 'a');

        if (isset($data["firstName"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["firstName"])) {
                $qb = $qb->andWhere('a.firstName LIKE \'' . $data["firstName"] . '%\''); //print an error or something if one of them exists.
            }
        }

        if (isset($data["lastName"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["lastName"])) {
                $qb = $qb->andWhere('a.lastName LIKE \'' . $data["lastName"] . '%\''); //print an error or something if one of them exists.
            }
        }

        if (isset($data["role"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["role"])) {
                $qb = $qb->innerJoin(UsersRoles::class, 'u', 'WITH', 'u.role_id = '.$data["role"]);
                $qb = $qb->andWhere('a.admin_ID = u.user_id');
            }
        }

        if (isset($data["enabled"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["enabled"])) {
                $qb = $qb->andWhere('a.enabled = '.$data["enabled"]);
            }
        }

        if (isset($data["timestamp"])) {
                $qb = $qb->andWhere('a.timeStamp = \'' . $data["timestamp"]->format('Y-m-d H:i:s').'\'');
        }

        $qb = $qb->setFirstResult($perPage*($pageNumber-1))
            ->setMaxResults($perPage);

        $qb = $qb->andWhere('a.is_deleted != 1');

        $qb = $qb->getQuery()->execute();

        for($i = 0; $i < sizeof($qb); $i++){
            $sql = 'SELECT admin_users.enabled, admin_users.time_stamp, admin_users.admin_id, admin_users.email, admin_users.first_name, admin_users.last_name
              FROM admin_users

              WHERE admin_users.admin_id = '.$qb[$i]->getAdminID();


            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $admin = $stmt->fetch();

            $sql = 'SELECT role_name FROM roles INNER JOIN users_roles ON users_roles.user_id = '.$qb[$i]->getAdminID().' AND users_roles.role_id = roles.role_id ';
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $rolesAdmin = implode(',', $stmt->fetchAll(7));

            $admin["role_name"] = $rolesAdmin;

            array_push($admins, $admin);
        }

        return $admins;
    }

    public function countPages($data,$perPage){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(a)')
            ->from('AppBundle:admin', 'a');

        if (isset($data["firstName"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["firstName"])) {
                $qb = $qb->andWhere('a.firstName LIKE \'' . $data["firstName"] . '%\''); //print an error or something if one of them exists.
            }
        }

        if (isset($data["lastName"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["lastName"])) {
                $qb = $qb->andWhere('a.lastName LIKE \'' . $data["lastName"] . '%\''); //print an error or something if one of them exists.
            }
        }

        if (isset($data["role"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["role"])) {
                $qb = $qb->innerJoin(UsersRoles::class, 'u', 'WITH', 'u.role_id = '.$data["role"]);
                $qb = $qb->andWhere('a.admin_ID = u.user_id');
            }
        }

        if (isset($data["enabled"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["enabled"])) {
                $qb = $qb->andWhere('a.enabled = ' . $data["enabled"]);
            }
        }

        if (isset($data["timestamp"])) {
            $qb = $qb->andWhere('a.timeStamp = \'' . $data["timestamp"]->format('Y-m-d H:i:s').'\'');
        }

        $qb = $qb->andWhere('a.is_deleted != 1');

        $qb = $qb->getQuery()->execute();

        $numberPages = ceil($qb[0][1] / $perPage);
        return $numberPages;
    }

    public function getUserRoles($userID){
        $repoUserRoles = $this->getEntityManager()->getRepository('AppBundle:UsersRoles');

        $userRoles = $repoUserRoles->getRolesById($userID);

        return $userRoles;
    }
}
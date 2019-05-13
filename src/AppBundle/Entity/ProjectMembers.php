<?php
/**
 * Created by PhpStorm.
 * User: Ali Fakhoury
 * Date: 1/15/2019
 * Time: 6:43 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Message
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\membersRepository")
 * @ORM\Table(name="project_members")
 */
class ProjectMembers{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $project_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getProjectId()
    {
        return $this->project_id;
    }

    /**
     * @param mixed $project_id
     */
    public function setProjectId($project_id)
    {
        $this->project_id = $project_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
}
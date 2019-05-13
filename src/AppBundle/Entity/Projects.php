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
 * @ORM\Entity(repositoryClass="AppBundle\Entity\projectsRepository")
 * @ORM\Table(name="projects")
 */
class Projects
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $project_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $project_name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $head_of_project;

    /**
     * @ORM\Column(type="date")
     */
    protected $date_of_project;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @ORM\Column(type="integer")
     */
    protected $is_deleted;

    /**
     * @param mixed $date_of_project
     */
    public function setDateOfProject($date_of_project)
    {
        $this->date_of_project = $date_of_project;
    }

    /**
     * @return mixed
     */
    public function getDateOfProject()
    {
        return $this->date_of_project;
    }

    /**
     * @return mixed
     */
    public function getProjectID()
    {
        return $this->project_ID;
    }

    /**
     * @param mixed $project_ID
     */
    public function setProjectID($project_ID)
    {
        $this->project_ID = $project_ID;
    }

    /**
     * @return mixed
     */
    public function getProjectName()
    {
        return $this->project_name;
    }

    /**
     * @param mixed $project_name
     */
    public function setProjectName($project_name)
    {
        $this->project_name = $project_name;
    }

    /**
     * @return mixed
     */
    public function getHeadOfProject()
    {
        return $this->head_of_project;
    }

    /**
     * @param mixed $head_of_project
     */
    public function setHeadOfProject($head_of_project)
    {
        $this->head_of_project = $head_of_project;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getisDeleted()
    {
        return $this->is_deleted;
    }

    /**
     * @param mixed $is_deleted
     */
    public function setIsDeleted($is_deleted)
    {
        $this->is_deleted = $is_deleted;
    }
}
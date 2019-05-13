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
 * @ORM\Entity(repositoryClass="AppBundle\Entity\positionsRepository")
 * @ORM\Table(name="positions")
 */
class Positions
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $position_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $position_name;

    /**
     * @return mixed
     */
    public function getPositionID()
    {
        return $this->position_ID;
    }

    /**
     * @param mixed $position_ID
     */
    public function setPositionID($position_ID)
    {
        $this->position_ID = $position_ID;
    }

    /**
     * @return mixed
     */
    public function getPositionName()
    {
        return $this->position_name;
    }

    /**
     * @param mixed $position_name
     */
    public function setPositionName($position_name)
    {
        $this->position_name = $position_name;
    }
}
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Event
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\eventRepository")
 * @ORM\Table(name="events")
 */

class Event{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $event_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $event_name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $event_type_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @ORM\Column(type="date")
     */
    protected $event_date;


    /**
     *@ORM\Column(type="integer")
     */
    protected $is_deleted;

    /**
     * @return mixed
     */
    public function getEventID()
    {
        return $this->event_ID;
    }

    /**
     * @param mixed $event_ID
     */
    public function setEventID($event_ID)
    {
        $this->event_ID = $event_ID;
    }

    /**
     * @return mixed
     */
    public function getEventName()
    {
        return $this->event_name;
    }

    /**
     * @param mixed $event_name
     */
    public function setEventName($event_name)
    {
        $this->event_name = $event_name;
    }

    /**
     * @return mixed
     */
    public function getEventTypeID()
    {
        return $this->event_type_ID;
    }

    /**
     * @param mixed $event_type_ID
     */
    public function setEventTypeID($event_type_ID)
    {
        $this->event_type_ID = $event_type_ID;
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
    public function getEventDate()
    {
        return $this->event_date;
    }

    /**
     * @param mixed $event_date
     */
    public function setEventDate($event_date)
    {
        $this->event_date = $event_date;
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

?>
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EventStatus
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="event_types")
 */

class EventType{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $event_type_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $event_type_name;

    /**
     * @return mixed
     */
    public function getEventTypeId()
    {
        return $this->event_type_id;
    }

    /**
     * @param mixed $event_type_id
     */
    public function setEventTypeId($event_type_id)
    {
        $this->event_type_id = $event_type_id;
    }

    /**
     * @return mixed
     */
    public function getEventTypeName()
    {
        return $this->event_type_name;
    }

    /**
     * @param mixed $event_type_name
     */
    public function setEventTypeName($event_type_name)
    {
        $this->event_type_name = $event_type_name;
    }
}

?>

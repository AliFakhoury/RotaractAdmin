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
 * @ORM\Entity(repositoryClass="AppBundle\Entity\projectImagesRepository")
 * @ORM\Table(name="images")
 */
class Images{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $image_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $image_Url;

    /**
     * @ORM\Column(type="string")
     */
    protected $thumbnail_Url;

    /**
     * @ORM\Column(type="integer")
     */
    protected $project_ID;

    /**
     * @ORM\Column(type="integer")
     */
    protected $is_deleted;

    /**
     * @return mixed
     */
    public function getImageID()
    {
        return $this->image_ID;
    }

    /**
     * @param mixed $image_ID
     */
    public function setImageID($image_ID)
    {
        $this->image_ID = $image_ID;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->image_Url;
    }

    /**
     * @param mixed $image_Url
     */
    public function setImageUrl($image_Url)
    {
        $this->image_Url = $image_Url;
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
    public function getThumbnailUrl()
    {
        return $this->thumbnail_Url;
    }

    /**
     * @param mixed $thumbnail_Url
     */
    public function setThumbnailUrl($thumbnail_Url)
    {
        $this->thumbnail_Url = $thumbnail_Url;
    }


}
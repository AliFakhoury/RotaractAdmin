<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\userRepository")
 * @ORM\Table(name="users")
 */
class User implements UserInterface {
    private $plainedPassword;
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $user_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $user_email;

    /**
     * @ORM\Column(type="string")
     */
    protected $first_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $last_name;

    /**
     * @ORM\Column(type="string")
     */

    protected $password;

    /**
     * @ORM\Column(type="integer")
     */
    protected $country_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $position_id;

    /**
     * @ORM\Column(type="date")
     */
    protected $birth_date;

    /**
     * @ORM\Column(type="integer")
     */
    protected $status_id;

    /**
     * @ORM\Column(type="date")
     */
    protected $registration_Date;


    /**
     *@ORM\Column(type="integer")
     */
    protected $is_deleted;

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getUserEmail()
    {
        return $this->user_email;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * @return mixed
     */
    public function getBirthDate()
    {
        return $this->birth_date;
    }

    /**
     * @return mixed
     */
    public function getStatusId()
    {
        return $this->status_id;
    }

    /**
     * @return mixed
     */
    public function getRegistrationDate()
    {
        return $this->registration_Date;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @param mixed $user_email
     */
    public function setUserEmail($user_email)
    {
        $this->user_email = $user_email;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param mixed $country_id
     */
    public function setCountryId($country_id)
    {
        $this->country_id = $country_id;
    }

    /**
     * @param mixed $birth_date
     */
    public function setBirthDate($birth_date)
    {
        $this->birth_date = $birth_date;
    }

    /**
     * @param mixed $status_id
     */
    public function setStatusId($status_id)
    {
        $this->status_id = $status_id;
    }

    /**
     * @param mixed $registration_Date
     */
    public function setRegistrationDate($registration_Date)
    {
        $this->registration_Date = $registration_Date;
    }

    /**
     * @return mixed
     */
    public function getPlainedPassword()
    {
        return $this->plainedPassword;
    }

    /**
     * @param mixed $plainedPassword
     */
    public function setPlainedPassword($plainedPassword)
    {
        $this->plainedPassword = $plainedPassword;
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

    /**
     * @return mixed
     */
    public function getPositionId()
    {
        return $this->position_id;
    }

    /**
     * @param mixed $position_id
     */
    public function setPositionId($position_id)
    {
        $this->position_id = $position_id;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return array('ROLE_USER');
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}

?>
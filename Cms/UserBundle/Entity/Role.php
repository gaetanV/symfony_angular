<?php

namespace Cms\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
class Role 
{
    
    Const REPOSITORY = "CmsUserBundle:Role";
    static $roles=array("ROLE_USER","ROLE_EDITOR") ;
     
    /**
     * @var integer
     */
    private $type;

    /**
     * @var \Cms\CoreBundle\Entity\User
     */
    private $user;


    /**
     * Set type
     *
     * @param integer $type
     * @return Role
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

   
    public function getRole()
    {
            return self::$roles[$this->type];
    }
    
    /**
     * Set user
     *
     * @param \Cms\CoreBundle\Entity\User $user
     * @return Role
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Cms\CoreBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * @var integer
     */
    private $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}

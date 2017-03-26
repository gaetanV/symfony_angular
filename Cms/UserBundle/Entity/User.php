<?php

namespace Cms\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * User
 */
class User implements UserInterface
{
      Const REPOSITORY = "CmsUserBundle:User";
    
        
    /***
     * 
     * Listener
     */
    
    public function preEncodePass()
    {
            $this->password = md5($this->password);
    }
    
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        
        
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
       
       $this->password =$password;
        return $this;
    }
    

    

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    public function getSalt(){
        return null;
    }
    
    public function getRoles(){
            $return =array();
            foreach ($this->roles as $key => $value) {
                \array_push($return, $value->getRole());                
            }
            return $return;
    }
    
    public function eraseCredentials(){
        
    }
    
    public function isPasswordLegal()
    {
      return $this->username !== $this->password;
    }
    

    /**
     * @var integer
     */
    private $type;


    /**
     * Set type
     *
     * @param integer $type
     * @return User
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
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $roles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $role = new Role;
        $role->setType(0);
        
        $this->addRole($role);

    }

    /**
     * Add roles
     *
     * @param \Cms\UserBundle\Entity\Role $roles
     * @return User
     */
    public function addRole(Role $roles)
    {
        $roles->setUser($this);
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Cms\UserBundle\Entity\Role $roles
     */
    public function removeRole(Role $roles)
    {
        $this->roles->removeElement($roles);
    }
}

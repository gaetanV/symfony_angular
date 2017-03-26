<?php

namespace Cms\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Translation
{
    Const REPOSITORY = "CmsContentBundle:Translation";
    static $langage_list=array("fr","es","en") ;
    
    /**
     * @var integer
     */
    private $id;
    

    /**
     * @var string
     */
    private $langage;
    
    public $content;
    
    public function __toString()
    {
       return $this->value;
    }  

    /**
     * @var string
     */
    private $value;


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
     * Set langage
     *
     * @param integer $langage
     * @return Translation
     */
    public function setLangage($langage)
    {
        $this->langage = $langage;

        return $this;
    }

    /**
     * Get langage
     *
     * @return integer 
     */
    public function getLangage()
    {
        return $this->langage;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Translation
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set content
     *
     * @param \Cms\ContentBundle\Entity\Content $content
     * @return Translation
     */
    public function setContent(Content $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return \Cms\ContentBundle\Entity\Content 
     */
    public function getContent()
    {
        return $this->content;
    }
    
    

}

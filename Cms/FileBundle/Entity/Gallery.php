<?php

namespace Cms\FileBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

class Gallery {
    Const REPOSITORY = "CmsFileBundle:Gallery";
    
    private $name;
    private $id;
    public $files;

    /**
     * Constructor
     */
    public function __construct() {
        $this->files = new ArrayCollection();
    }

    public function __toString() {
        return $this->name;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
 


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
     * Set name
     *
     * @param string $name
     * @return Gallery
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add files
     *
     * @param \Cms\FileBundle\Entity\File $files
     * @return Gallery
     */
    public function addFile(File $files)
    {
        $this->files[] = $files;
        
        return $this;
    }

    /**
     * Remove files
     *
     * @param \Cms\FileBundle\Entity\File $files
     */
    public function removeFile(File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }
}

<?php

namespace Cms\FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


class File 
{
       Const REPOSITORY = "CmsFileBundle:File";
         /***
     * Attributs
      //////////////////////////////////////////////////
     */
    
    public $file;
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;
    
  

    /**
     * @var string
     */
    protected $path;
    
    
    
     /***
     * Path
      //////////////////////////////////////////////////
     */
        public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/documents';
    }

    
     /***
     * Listener
      //////////////////////////////////////////////////
     */
    
    public function removeUpload()
    {
            if ($file = $this->getAbsolutePath()) {@unlink($file);}
    }


    public function preUpload()
    {
      
        if (null !== $this->file) {
       
            // faites ce que vous voulez pour générer un nom unique
            $this->path = sha1(uniqid(mt_rand(), true)).'.jpg';
                    //.$this->file->guessExtension();
        }
    }

    public function upload()
    {
        if (null === $this->file) {
            return;
        }
      if(strstr ($this->file, sys_get_temp_dir())){
              copy($this->file,$this->getAbsolutePath());
      }
       //$this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
       // $this->path = $this->file->getClientOriginalName();
        $this->file = null;
    }
    
    
        
     /***
     * Setters and Getters
      //////////////////////////////////////////////////
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
     * @return File
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
     * Set path
     *
     * @param string $path
     * @return File
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }


    
    public function __toString()
    {
       return $this->name;
    }  

   
    private $translations;
    
    
     public function __construct() {
             $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
     }


     

   
}

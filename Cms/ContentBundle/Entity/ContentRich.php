<?php
namespace Cms\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

class ContentRich {
   Const REPOSITORY = "CmsContentBundle:ContentRich";
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $translations;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $files;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->files = new ArrayCollection();
        foreach (Translation::$langage_list as $key => $value) {
            $translation = new TranslationRich();
            $translation->setLangage($value);
            $this->addTranslation($translation);
        }
        
    }

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
     * Add translations
     *
     * @param \Cms\ContentBundle\Entity\TranslationRich $translations
     * @return ContentRich
     */
    public function addTranslation(TranslationRich $translations)
    {
        $translations->setContent($this);
        $this->translations[] = $translations;

        return $this;
    }

    /**
     * Remove translations
     *
     * @param \Cms\ContentBundle\Entity\TranslationRich $translations
     */
    public function removeTranslation(TranslationRich $translations)
    {
        $this->translations->removeElement($translations);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTranslations()
    {
        return $this->translations;
    }

  
}

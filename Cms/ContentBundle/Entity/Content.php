<?php
namespace Cms\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

class Content {
    Const REPOSITORY = "CmsContentBundle:Content";
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Cms\ContentBundle\Entity\Translation
     */
    private $translations;

    /**
     * Constructor
     */
    public function __construct() {
        $this->translations = new ArrayCollection();
        
        foreach (Translation::$langage_list as $key => $value) {
            $translation = new Translation();
            $translation->setLangage($value);
            $this->addTranslation($translation);
        }
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Add translations
     *
     * @param \Cms\ContentBundle\Entity\Translation $translations
     * @return Content
     */
    public function addTranslation(Translation $translations) {
        $translations->setContent($this);
        $this->translations[] = $translations;
        return $this;
    }

    /**
     * Remove translations
     *
     * @param \Cms\ContentBundle\Entity\Translation $translations
     */
    public function removeTranslation(Translation $translations) {
        $this->translations->removeElement($translations);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTranslations() {
        return $this->translations;
    }

    public function __toString() {
        return (string) $this->id;
    }

}

<?php

namespace Cms\TaxonomyBundle\Entity;
use Cms\ContentBundle\Entity\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

class Category {
     Const REPOSITORY = "CmsTaxonomyBundle:Category";
    
    
    public function __toString(){
        /*
            $names= $this->getName();
            $names=$names->getTranslations();
            $names->initialize();
            return $names[0]->getValue();
        */
        return (string)$this->id;
    }
    

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Cms\ContentBundle\Entity\Content
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \Cms\TaxonomyBundle\Entity\Category
     */
    private $parent;

    /**
     * Constructor
     */
    public function __construct() {

        $this->children = new ArrayCollection();
        $this->name = new Content();
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
     * Set name
     *
     * @param \Cms\ContentBundle\Entity\Content $name
     * @return Category
     */
    public function setName(Content $name = null) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return \Cms\ContentBundle\Entity\Content 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Cms\TaxonomyBundle\Entity\Category $parent
     * @return Category
     */
    public function setParent(Category $parent = null) {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Cms\TaxonomyBundle\Entity\Category 
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Set parent_ID
     *
     * @param integer $parentID
     * @return Category
     */
    public function setParentID($parentID) {
        $this->parent_ID = $parentID;

        return $this;
    }

    /**
     * Get parent_ID
     *
     * @return integer 
     */
    public function getParentID() {
        return $this->parent_ID;
    }

    /**
     * Add children
     *
     * @param \Cms\TaxonomyBundle\Entity\Category $children
     * @return Category
     */
    public function addChild(Category $children) {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Cms\TaxonomyBundle\Entity\Category $children
     */
    public function removeChild(Category $children) {
        $this->children->removeElement($children);
    }

}

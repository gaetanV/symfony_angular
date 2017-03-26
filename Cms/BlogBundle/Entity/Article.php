<?php

namespace Cms\BlogBundle\Entity;

use Cms\ContentBundle\Entity\Content;
use Cms\ContentBundle\Entity\ContentRich;
use Cms\TaxonomyBundle\Entity\Category;

use Cms\FileBundle\Entity\Gallery;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

class Article {
    
    Const REPOSITORY = "CmsBlogBundle:Article";
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Cms\BlogBundle\Entity\Content
     */
    private $title;

    /**
     * @var \Cms\BlogBundle\Entity\Content
     */
    private $description;

    /**
     * @var \Cms\BlogBundle\Entity\Content
     */
    private $content;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $category;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->title = new Content();
        $this->description = new Content();
        $this->content = new ContentRich();
        $gallery=new Gallery();
        $gallery->setName("article_content");
        $this->setGallery($gallery);
        
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
     * Set title
     *
     * @param \Cms\BlogBundle\Entity\Content $title
     * @return Article
     */
    public function setTitle(Content $title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return \Cms\BlogBundle\Entity\Content 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param \Cms\BlogBundle\Entity\Content $description
     * @return Article
     */
    public function setDescription(Content $description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return \Cms\BlogBundle\Entity\Content 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set content
     *
     * @param \Cms\BlogBundle\Entity\Content $content
     * @return Article
     */
    public function setContent(Content $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return \Cms\BlogBundle\Entity\Content 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Add category
     *
     * @param \Cms\TaxonomyBundle\Entity\Category $category
     * @return Article
     */
    public function addCategory(Category $category)
    {
        
        $this->category[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Cms\TaxonomyBundle\Entity\Category $category
     */
    public function removeCategory(Category $category)
    {
        $this->category->removeElement($category);
    }

    /**
     * Get category
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * @var \Cms\FileBundle\Entity\Gallery
     */
    private $gallery;


    /**
     * Set gallery
     *
     * @param \Cms\FileBundle\Entity\Gallery $gallery
     * @return Article
     */
    public function setGallery(Gallery $gallery = null)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get gallery
     *
     * @return \Cms\FileBundle\Entity\Gallery 
     */
    public function getGallery()
    {
        return $this->gallery;
    }
}

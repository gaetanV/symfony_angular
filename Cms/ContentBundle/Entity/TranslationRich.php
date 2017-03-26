<?php

namespace Cms\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


class TranslationRich
{
   Const REPOSITORY = "CmsContentBundle:TranslationRich";


    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $langage;

    /**
     * @var string
     */
    private $value;

    /**
     * @var \Cms\CoreBundle\Entity\ContentRich
     */
    private $content;


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
     * @param string $langage
     * @return TranslationRich
     */
    public function setLangage($langage)
    {
        $this->langage = $langage;

        return $this;
    }

    /**
     * Get langage
     *
     * @return string 
     */
    public function getLangage()
    {
        return $this->langage;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return TranslationRich
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
     * @param \Cms\ContentBundle\Entity\ContentRich $content
     * @return TranslationRich
     */
    public function setContent(ContentRich $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return \Cms\ContentBundle\Entity\ContentRich 
     */
    public function getContent()
    {
        return $this->content;
    }
}

<?php

namespace Hp\MmFlashBanner\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="mm_banners_flash_section")
 */
class Section
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="title", type="string")
     */
    private $title;


    /**
     * @var string
     * @ORM\Column(name="description", type="string")
     */
    private $description;


    /**
     * @var string
     * @ORM\Column(name="short_description", type="string")
     */
    private $shortDescription;

    public function getId() :int
    {
        return $this->id;
    }
    public function getTitle() :?string
    {
        return $this->title;
    }
    public function setTitle(string $title):self
    {
        $this->title = $title;
        return $this;
    }
    public function getDescription() :?string
    {
        return $this->description;
    }
    public function setDescription(string $description):self
    {
        $this->description = $description;
        return $this;
    }
    public function getShortDescription() :?string
    {
        return $this->shortDescription;
    }
    public function setShortDescription(string $shortDescription):self
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }



}
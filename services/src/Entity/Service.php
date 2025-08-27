<?php
declare(strict_types=1);

namespace Hp\Services\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="mm_services")
 */
class Service
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="image",type="string", length=255)
     */
    private $image ;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $description;
    /**
     * @ORM\Column(type="integer",name="active")
     */
    private $active ;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;


    public function __construct()
    {
        $this->createdAt = new \DateTime();

    }

    // Getters and setters for the properties
    public function getId(): int
    {
        return $this->id;
    }

    public function setImage( $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }
    public function setTitle(? string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getActive(){
        return $this->active;
    }

    public function setActive($active){
        $this->active = $active;
        return $this;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }


    
}

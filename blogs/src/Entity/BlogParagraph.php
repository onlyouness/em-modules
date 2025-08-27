<?php

namespace Hp\Blogs\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table()
 */

class BlogParagraph
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */

    private int $id;

    /**
     *   @ORM\ManyToOne(targetEntity= "Hp\Blogs\Entity\Blog", inversedBy = "paragraph")
     *   @ORM\JoinColumn(name= "id_blog", referencedColumnName= "id",onDelete="CASCADE")
     */
    private ?Blog $blog = null;

    /**

     * @ORM\Column(type="string", length=255)

     */

    private string $title;

    /**

     * @ORM\Column(type="text")

     */

    private string $description;

    /**

     * @ORM\Column(type="string", length=255)

     */
    

    private string  $image;

    /**

     * @ORM\Column(type="datetime")

     */

    private \DateTime $createdAt;

    public function __construct()
    {

        $this->createdAt = new \DateTime();

    }

    public function getId(): int
    {

        return $this->id;
    }

    public function setTitle(string $title): self
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

    public function setImage(string $image): self
    {

        $this->image = $image;

        return $this;
    }

    public function getImage(): string
    {

        return $this->image;
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

    public function getBlog(): ?Blog
    {

        return $this->blog;
    }

    public function setBlog(?Blog $blog): self
    {

        $this->blog = $blog;

        return $this;
    }

    public function __toString()
    {

        return $this->getTitle();
    }
}

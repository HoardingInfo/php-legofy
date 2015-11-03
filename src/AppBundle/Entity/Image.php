<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="image")
 */
class Image
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank(message="Le nom de la playlist ne peut pas être vide.")
     */
    private $name;

    /**
     * @ORM\Column(name="private", type="boolean")
     */
    private $private;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @return int
     */
    public function getId() 
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() 
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name) 
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isPrivate() 
    {
        return $this->private;
    }

    /**
     * @param boolean $private
     * @return $this
     */
    public function setPrivate($private) 
    {
        $this->private = $private;
        return $this;
    }

    /**
     * @param \DateTime $creationDate
     * @return $this
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }
}

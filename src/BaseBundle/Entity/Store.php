<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Store
 *
 * @ORM\Table(name="store", options={"comment"="门店"})
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\StoreRepository")
 */
class Store
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, options={"comment"="门店名称"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, options={"comment"="门店地址"})
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="time", type="string", length=255, options={"comment"="营业时间"})
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, options={"default" = "", "comment"="门店照片"})
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, options={"comment"="门店电话"})
     */
    private $phone;

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
     * @return Store
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
}

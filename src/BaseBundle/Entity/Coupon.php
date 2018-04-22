<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Coupon
 *
 * @ORM\Table(name="coupon", options={"comment"="优惠券"})
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\CouponRepository")
 */
class Coupon
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
     * @ORM\Column(name="name", type="string", length=255, options={"comment"="优惠券名称"})
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", options={"default" = "1", "comment"="类型(1： 金额优惠券， 2：非金额优惠券)"})
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="amount",  type="decimal", precision=10, scale=2, options={ "default"=0 ,"comment"="优惠券金额"})
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column (name="day", type="integer", options={"comment"="优惠券时长"})
     */
    private $day;

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
     * @return Coupon
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

    /**
     * Set amount
     *
     * @param integer $amount
     * @return Coupon
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set day
     *
     * @param integer $day
     * @return Coupon
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return integer 
     */
    public function getDay()
    {
        return $this->day;
    }
}

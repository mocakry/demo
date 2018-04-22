<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OfflineCard
 *
 * @ORM\Table(name="offline_card", options={"comment"="线下实体卡"})
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\OfflineCardRepository")
 */
class OfflineCard
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
     * @ORM\Column(name="name", type="string", length=255, options={"comment"="线下实体卡名称"})
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="integer", options={"comment"="等级(1:A 2:B 3:C 4:D)"})
     */
    private $rank;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer", options={"comment"="线下实体卡生成子卡的数量"})
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="card_id", type="string", length=255, options={"comment"="父实体卡的编号"})
     */
    private $cardId;
    /**
     * @var int
     *
     * @ORM\Column(name="coupon_id", type="integer", options={"comment"="优惠券的id"})
     */
    private $couponId;

    /**
     * @var int
     *
     * @ORM\Column(name="coupon_number", type="integer", options={"comment"="该优惠券的个数"})
     */
    private $couponNumber;


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
     * @return OfflineCard
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
     * Set rank
     *
     * @param integer $rank
     * @return OfflineCard
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer 
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return OfflineCard
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set couponId
     *
     * @param integer $couponId
     * @return OfflineCard
     */
    public function setCouponId($couponId)
    {
        $this->couponId = $couponId;

        return $this;
    }

    /**
     * Get couponId
     *
     * @return integer 
     */
    public function getCouponId()
    {
        return $this->couponId;
    }
}

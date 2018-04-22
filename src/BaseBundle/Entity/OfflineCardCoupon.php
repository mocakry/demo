<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OfflineCardCoupon
 *
 * @ORM\Table(name="offline_card_coupon", options={"comment"="父实体卡和优惠券"})
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\OfflineCardCouponRepository")
 */
class OfflineCardCoupon
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
     * Set cardId
     *
     * @param integer $cardId
     * @return OfflineCardCoupon
     */
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;

        return $this;
    }

    /**
     * Get cardId
     *
     * @return integer 
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * Set couponId
     *
     * @param integer $couponId
     * @return OfflineCardCoupon
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

    /**
     * Set couponNumber
     *
     * @param integer $couponNumber
     * @return OfflineCardCoupon
     */
    public function setCouponNumber($couponNumber)
    {
        $this->couponNumber = $couponNumber;

        return $this;
    }

    /**
     * Get couponNumber
     *
     * @return integer 
     */
    public function getCouponNumber()
    {
        return $this->couponNumber;
    }
}

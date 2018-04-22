<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserCoupon
 *
 * @ORM\Table(name="user_coupon", options={"comment"="用户的优惠券"})
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\UserCouponRepository")
 */
class UserCoupon
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
     * @var int
     *
     * @ORM\Column(name="coupon_id", type="integer", options={"comment"="优惠券id"})
     */
    private $couponId;

    /**
     * @var string
     *
     * @ORM\Column(name="coupon_number", type="string", length=255, options={"comment"="优惠券编号"})
     */
    private $couponNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", options={"comment"="用户id"})
     */
    private $userId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_used", type="boolean", options={"comment"="是否使用（0：未使用， 1：已使用）", "default"=0})
     */
    private $isUsed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="add_date", type="datetime", options={"comment"="优惠券添加时间（优惠券开始时间）"})
     */
    private $addDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", options={"comment"="优惠券失效时间"})
     */
    private $endDate;

    /**
     * @var int
     *
     * @ORM\Column(name="admin_id", type="integer", options={"comment"="操作的管理员", "default"="0"})
     */
    private $adminId;

    /**
     * @var int
     *
     * @ORM\Column(name="storesOwned", type="integer", options={"comment"="管理员所属门店", "default"="0"})
     */
    private $storesOwned;

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
     * Set couponId
     *
     * @param integer $couponId
     * @return UserCoupon
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
     * @param string $couponNumber
     * @return UserCoupon
     */
    public function setCouponNumber($couponNumber)
    {
        $this->couponNumber = $couponNumber;

        return $this;
    }

    /**
     * Get couponNumber
     *
     * @return string 
     */
    public function getCouponNumber()
    {
        return $this->couponNumber;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserCoupon
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set isUsed
     *
     * @param boolean $isUsed
     * @return UserCoupon
     */
    public function setIsUsed($isUsed)
    {
        $this->isUsed = $isUsed;

        return $this;
    }

    /**
     * Get isUsed
     *
     * @return boolean 
     */
    public function getIsUsed()
    {
        return $this->isUsed;
    }

    /**
     * Set addDate
     *
     * @param \DateTime $addDate
     * @return UserCoupon
     */
    public function setAddDate($addDate)
    {
        $this->addDate = $addDate;

        return $this;
    }

    /**
     * Get addDate
     *
     * @return \DateTime 
     */
    public function getAddDate()
    {
        return $this->addDate;
    }
}

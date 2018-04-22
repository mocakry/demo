<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserConsumption
 *
 * @ORM\Table(name="user_consumption", options={"comment"="用户消费表"})
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\UserConsumptionRepository")
 */
class UserConsumption
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
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, options={"comment"="消费金额"})
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column(name="point_type", type="integer", options={"comment"="积分类型（1：增加积分； 2：减少积分）"})
     */
    private $pointType;

    /**
     * @var string
     *
     * @ORM\Column(name="point_limit", type="string", length=255, options={"comment"="积分额度"})
     */
    private $pointLimit;

    /**
     * @var int
     *
     * @ORM\Column(name="admin_id", type="integer", options={"comment"="操作的管理员"})
     */
    private $adminId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="add_date", type="datetime", options={"comment"="添加时间"})
     */
    private $addDate;

    /**
     * @var string
     *
     * @ORM\Column(name="mark", type="string", length=255, options={"comment"="备注"})
     */
    private $mark;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", options={"comment"="用户id"})
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="storesOwned", type="integer", options={"comment"="管理员所属门店"})
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
     * Set name
     *
     * @param string $name
     * @return UserConsumption
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
     * @param string $amount
     * @return UserConsumption
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set pointType
     *
     * @param integer $pointType
     * @return UserConsumption
     */
    public function setPointType($pointType)
    {
        $this->pointType = $pointType;

        return $this;
    }

    /**
     * Get pointType
     *
     * @return integer 
     */
    public function getPointType()
    {
        return $this->pointType;
    }

    /**
     * Set pointLimit
     *
     * @param string $pointLimit
     * @return UserConsumption
     */
    public function setPointLimit($pointLimit)
    {
        $this->pointLimit = $pointLimit;

        return $this;
    }

    /**
     * Get pointLimit
     *
     * @return string 
     */
    public function getPointLimit()
    {
        return $this->pointLimit;
    }

    /**
     * Set adminId
     *
     * @param integer $adminId
     * @return UserConsumption
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;

        return $this;
    }

    /**
     * Get adminId
     *
     * @return integer 
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * Set addDate
     *
     * @param \DateTime $addDate
     * @return UserConsumption
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

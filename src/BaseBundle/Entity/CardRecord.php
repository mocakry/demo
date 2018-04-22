<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CardRecord
 *
 * @ORM\Table(name="card_record", options={"comment"="用户充值记录"})
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\CardRecordRepository")
 */
class CardRecord
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
     * @ORM\Column(name="user_id", type="integer", options={"comment"="用户id"})
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="sub_card_id", type="string", length=255, unique=true, options={"comment"="子卡的编号"})
     */
    private $subCardId;


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
     * Set userId
     *
     * @param integer $userId
     * @return CardRecord
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
     * Set subCardId
     *
     * @param string $subCardId
     * @return CardRecord
     */
    public function setSubCardId($subCardId)
    {
        $this->subCardId = $subCardId;

        return $this;
    }

    /**
     * Get subCardId
     *
     * @return string 
     */
    public function getSubCardId()
    {
        return $this->subCardId;
    }
}

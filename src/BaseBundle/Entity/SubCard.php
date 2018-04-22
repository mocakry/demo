<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubCard
 *
 * @ORM\Table(name="sub_card", options={"comment"="实体卡的子卡表"})
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\SubCardRepository")
 */
class SubCard
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
     * @ORM\Column(name="card_id", type="string", options={"comment"="子卡的父实体卡的编号"})
     */
    private $cardId;

    /**
     * @var string
     *
     * @ORM\Column(name="Sub_card_id", type="string", length=255, unique=true, options={"comment"="子卡的编号"})
     */
    private $subCardId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_used", type="boolean", options={"comment"="是否使用（0：未使用， 1：已使用）", "default"=0})
     */
    private $isUsed;


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
     * @return SubCard
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
     * Set subCardId
     *
     * @param string $subCardId
     * @return SubCard
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

    /**
     * Set isUsed
     *
     * @param boolean $isUsed
     * @return SubCard
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
}

<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Feedback
 *
 * @ORM\Table(name="feedback", options={"comment"="期待与反馈"})
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\FeedbackRepository")
 */
class Feedback
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
     * @ORM\Column(name="type", type="integer", options={"comment"="类型(1:用户期待 2：用户反馈)", "default"=0})
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", options={"comment"="内容"})
     */
    private $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="add_date", type="datetime", options={"comment"="添加时间"})
     */
    private $addDate;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", options={"comment"="用户id"})
     */
    private $userId;


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
     * Set type
     *
     * @param integer $type
     * @return Feedback
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Feedback
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set addDate
     *
     * @param \DateTime $addDate
     * @return Feedback
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

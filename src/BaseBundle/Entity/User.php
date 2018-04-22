<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user", options={"comment"="用户"})
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\UserRepository")
 */
class User
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
     * @ORM\Column(name="name", type="string", length=255, options={"default" = "", "comment"="用户名"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="string", length=255, options={"default" = "", "comment"="用户昵称"})
     */
    private $nickname;

    /**
     * @var string
     *
     * @ORM\Column(name="openid", type="string", length=255, nullable=true, unique=true, options={"comment"="微信openid"})
     */
    private $openid;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true, options={"comment"="头像"})
     */
    private $avatar;

    /**
     * @var int
     *
     * @ORM\Column(name="sex", type="integer", options={"default" = "0", "comment"="性别（0:未知； 1：男； 2：女）"})
     */
    private $sex;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, options={"default" = "", "comment"="手机号"})
     */
    private $phone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=true, options={ "comment"="生日"})
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="qq", type="string", length=255, options={"default" = "", "comment"="qq"})
     */
    private $qq;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=255, options={"default" = "", "comment"="邮箱"})
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="points", type="string", length=255, options={"default" = "", "comment"="积分"})
     */
    private $points;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="register_date", type="datetime", options={"comment"="注册时间"})
     */
    private $registerDate;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="integer", options={"default" = "0", "comment"="等级(1:A 2:B 3:C 4:D)"})
     */
    private $rank;

    /**
     * @var int
     *
     * @ORM\Column(name="group_id", type="integer", options={"comment"="会员分组", "default"=0})
     */
    private $groupId;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar_update", type="string", length=255, options={"default" = "", "comment"="更换后头像"})
     */
    private $avatarUpdate;

    /**
     * @var int
     *
     * @ORM\Column(name="change_birthday", type="integer", options={"comment"="生日修改次数", "default"=0})
     */
    private $changeBirthday;


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
     * @return User
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
     * Set nickname
     *
     * @param string $nickname
     * @return User
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string 
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set openid
     *
     * @param string $openid
     * @return User
     */
    public function setOpenid($openid)
    {
        $this->openid = $openid;

        return $this;
    }

    /**
     * Get openid
     *
     * @return string 
     */
    public function getOpenid()
    {
        return $this->openid;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set sex
     *
     * @param integer $sex
     * @return User
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return integer 
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set qq
     *
     * @param string $qq
     * @return User
     */
    public function setQq($qq)
    {
        $this->qq = $qq;

        return $this;
    }

    /**
     * Get qq
     *
     * @return string 
     */
    public function getQq()
    {
        return $this->qq;
    }

    /**
     * Set mail
     *
     * @param string $mail
     * @return User
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string 
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set points
     *
     * @param string $points
     * @return User
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return string 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set registerDate
     *
     * @param \DateTime $registerDate
     * @return User
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    /**
     * Get registerDate
     *
     * @return \DateTime 
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }
}

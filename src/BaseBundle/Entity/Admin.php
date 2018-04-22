<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Admin
 *
 * @ORM\Table(name="admin", options={"comment"="管理员"})
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\AdminRepository")
 */
class Admin
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
     * @ORM\Column(name="account", type="string", length=255, options={"comment"="管理员帐号"})
     */
    private $account;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, options={"comment"="密码"})
     */
    private $password;

    /**
     * @var int
     *
     * @ORM\Column(name="storesOwned", type="integer", options={"comment"="管理员所属门店"})
     */
    private $storesOwned;

    /**
     * @var string
     *
     * @ORM\Column(name="admin_mail", type="string", length=255, options={"comment"="密码"})
     */
    private $adminMail;



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
     * Set account
     *
     * @param string $account
     * @return Admin
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return string 
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Admin
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set storesOwned
     *
     * @param integer $storesOwned
     * @return Admin
     */
    public function setStoresOwned($storesOwned)
    {
        $this->storesOwned = $storesOwned;

        return $this;
    }

    /**
     * Get storesOwned
     *
     * @return integer 
     */
    public function getStoresOwned()
    {
        return $this->storesOwned;
    }
}

<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccessToken
 *
 * @ORM\Table(name="access_token", options={"comment"="微信token表"})
 * @ORM\Entity()
 */
class AccessToken
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
     * @ORM\Column(name="token", type="string", length=255, nullable=true, options={"comment"="access_token"})
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="expire", type="string", length=255, nullable=true, options={"comment"="添加时间戳"})
     */
    private $expire;

}

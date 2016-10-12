<?php

namespace Lincode\RestApi\Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Handler\ArrayCollectionHandler;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Member
 *
 * @ORM\Table(name="oauth_member")
 * @ORM\Entity()
 * @Serializer\ExclusionPolicy("all")
 * @UniqueEntity(fields={"email"},errorPath="email",message="Este Email ja esta em uso.")
 */
class Member extends OauthUser
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose
     * @Groups({"mobile"})
     */
    protected $id;


    public function getId(){
        return $this->id;
    }

    public function __construct(){
        parent::__construct();
    }
}
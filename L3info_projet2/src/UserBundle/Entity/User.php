<?php
/**
 * Created by PhpStorm.
 * User: jerome_skoda
 * Date: 26/04/2016
 * Time: 18:41
 */

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="User")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="\SalarieBundle\Entity\Salarie")
     */
    protected $salarie_id;

    /**
     * Set salarieId
     *
     * @param \SalarieBundle\Entity\Salarie $salarieId
     *
     * @return UserAccount
     */
    public function setSalarieId(\SalarieBundle\Entity\Salarie $salarieId = null)
    {
        $this->salarie_id = $salarieId;

        return $this;
    }

    /**
     * Get salarieId
     *
     * @return \SalarieBundle\Entity\Salarie
     */
    public function getSalarieId()
    {
        return $this->salarie_id;
    }
}

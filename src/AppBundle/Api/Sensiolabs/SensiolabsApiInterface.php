<?php
namespace AppBundle\Api\Sensiolabs;

use AppBundle\Model\SensiolabsUser;

interface SensiolabsApiInterface
{
    /**
     * @param string $login
     *
     * @return SensiolabsUser
     */
    public function getUser($login);
}

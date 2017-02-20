<?php

namespace features\Fake;

use AppBundle\Api\ContributorPage\ContributorPageApiInterface;
use AppBundle\Api\Sensiolabs\SensiolabsApiInterface;
use AppBundle\Model\SensiolabsUser;
use features\Aa\ATrends\Fake\FakeDataAware;

class SensiolabsApi implements SensiolabsApiInterface
{
    use FakeDataAware;

    /**
     * @param string $login
     *
     * @return SensiolabsUser
     */
    public function getUser($login)
    {
        $data = $this->findDataItemByPropertyValue('profile', 'login', $login);

        return SensiolabsUser::createFromArray($data);
    }
}

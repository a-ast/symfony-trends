<?php


namespace features\Fake;

use AppBundle\Api\ContributorPage\ContributorPageApiInterface;
use features\Aa\ATrends\Fake\FakeDataAware;

class ContributorPageApi implements ContributorPageApiInterface
{
    use FakeDataAware;

    /**
     * @param string $uri
     * @param $profileUri
     *
     * @return array
     */
    function getContributorLogins($uri, $profileUri)
    {
        $data = $this->getData('contributors');

        return array_column($data, 'login');
    }
}

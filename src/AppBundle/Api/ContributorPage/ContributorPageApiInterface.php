<?php

namespace AppBundle\Api\ContributorPage;

interface ContributorPageApiInterface
{
    /**
     * @param string $uri
     * @param $profileUri
     *
     * @return array
     */
    function getContributorLogins($uri, $profileUri);
}

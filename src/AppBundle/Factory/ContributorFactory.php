<?php

namespace AppBundle\Factory;

use AppBundle\Entity\Contributor;

class ContributorFactory
{
    /**
     * @param string $email
     *
     * @return Contributor
     */
    public function createFromEmail($email)
    {
        return new Contributor($email);
    }

    /**
     * @param array $data
     *
     * @return Contributor
     */
    public function createFromArray(array $data)
    {
        $contributor = new Contributor($data['email']);

        if (isset($data['name'])) {
            $contributor->setName($data['name']);
        }

        if (isset($data['github_login'])) {
            $contributor->setGithubLogin($data['github_login']);
        }

        return $contributor;
    }
}

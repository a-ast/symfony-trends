<?php

namespace AppBundle\Model;

class GithubFork
{
    public $data;

    /**
     * Constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function createFromGithubResponseData($fork)
    {
        return new self($fork);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}

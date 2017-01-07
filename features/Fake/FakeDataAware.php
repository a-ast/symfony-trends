<?php

namespace features\Fake;

trait FakeDataAware
{
    /**
     * @var array
     */
    private $fakeData = [];

    public function addData($dataType, array $data)
    {
        $this->fakeData[$dataType][] = $data;
    }

    public function findBy($dataType, $propertyName, $propertyValue)
    {
        foreach ($this->fakeData[$dataType] as $item) {
            if ($item[$propertyName] === $propertyValue) {
                return $item;
            }
        }

        return null;
    }
}

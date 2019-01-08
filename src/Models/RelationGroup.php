<?php

namespace SWRetail\Models;

use SWRetail\Http\Client;

class RelationGroup extends Model
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @api
     *
     * @return array[RelationGroup]
     */
    public static function getAll(): array
    {
        $path = 'rel_groups';

        $response = Client::requestApi('GET', $path);

        $groups = [];
        foreach ($response->json as $name) {
            $groups[] = new static($name);
        }

        return $groups;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->getName();
    }
}

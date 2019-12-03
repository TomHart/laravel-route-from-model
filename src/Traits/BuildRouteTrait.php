<?php

namespace TomHart\Routing\Traits;

use InvalidArgumentException;

trait BuildRouteTrait
{


    /**
     * Builds a route, providing a routeName property exists on the class.
     * @return string
     * @throws InvalidArgumentException;
     */
    public function buildRoute(): string
    {
        if (!property_exists($this, 'routeName')) {
            throw new InvalidArgumentException(BuildRouteTrait::class . ' requires a "routeName" property');
        }

        return route_from_model($this->routeName, $this);
    }
}

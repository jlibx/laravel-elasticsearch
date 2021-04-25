<?php


namespace Golly\Elastic\Hydrate;


use Golly\Hydrate\Entity as HydrateEntity;

/**
 * Class Entity
 * @package Golly\Elastic\Hydrate
 */
class Entity extends HydrateEntity
{

    /**
     * @return array
     */
    public static function mapping()
    {
        $self = new static();

        return $self->newReflection()->map($self);
    }

}

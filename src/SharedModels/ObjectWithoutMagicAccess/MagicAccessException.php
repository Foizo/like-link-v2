<?php declare(strict_types=1);
namespace App\SharedModels\ObjectWithoutMagicAccess;


use InvalidArgumentException;

class MagicAccessException extends InvalidArgumentException
{
    public static function create(object $object, string $property, string $hint = ''): MagicAccessException
    {
        return new static( sprintf(
            "Property %s::$%s doesn't exist" . ($hint ? " - {$hint}" : ""),
            get_class($object),
            $property
        ));
    }
}
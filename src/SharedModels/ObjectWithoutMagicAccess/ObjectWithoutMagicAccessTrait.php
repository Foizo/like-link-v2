<?php declare(strict_types=1);
namespace App\SharedModels\ObjectWithoutMagicAccess;

/**
 * Avoiding magic access to properties - probable bug in the code
 */
trait ObjectWithoutMagicAccessTrait
{
    public function __get(string $name)
    {
        throw MagicAccessException::create($this, $name);
    }

    public function __set(string $name, $value): void
    {
        throw MagicAccessException::create($this, $name);
    }

    public function __unset(string $name): void
    {
        throw MagicAccessException::create($this, $name);
    }

    public function __isset(string $name): bool
    {
        throw MagicAccessException::create($this, $name, "use property_exists() to check if property exists instead of isset");
    }
}
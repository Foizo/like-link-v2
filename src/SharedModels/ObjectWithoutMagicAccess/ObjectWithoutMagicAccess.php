<?php declare(strict_types=1);
namespace App\SharedModels\ObjectWithoutMagicAccess;

/**
 * Object avoiding magic access to properties - probable bug in the code
 */
abstract class ObjectWithoutMagicAccess
{
    use ObjectWithoutMagicAccessTrait;
}
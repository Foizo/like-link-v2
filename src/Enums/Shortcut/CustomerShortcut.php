<?php declare(strict_types=1);
namespace App\Enums\Shortcut;

enum CustomerShortcut:string
{
    const ENTITY_COLUMN_LENGTH = 20;

    const SHORTCUT_PATTERN = '~^[a-zA-Z0-9\-]+$~';

    const NOT_SPECIFIED = 'not-specified';
}

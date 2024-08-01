<?php declare(strict_types=1);
namespace App\Enums\Shortcut;

enum GeneratedShortcut: string
{
    const ENTITY_COLUMN_LENGTH = 13;

    const SHORTCUT_MAX_LENGTH = 8;
    const SHORTCUT_MIN_LENGTH = 3;

    const SHORTCUT_MAX_GENERATE_ITERATION = 5;

    const SHORTCUT_PATTERN = "~^[a-zA-Z0-9]+$~";

    const NOT_GENERATED = 'not-generated';
}

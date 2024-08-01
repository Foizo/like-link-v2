<?php declare(strict_types=1);
namespace App\Enums;

enum Domain: string
{
    const ENTITY_DOMAIN_COLUMN_LENGTH = 29;
    const ENTITY_LANG_COLUMN_LENGTH = 2;

    const APP_DOMAIN_PATTERN = '~^([a-z]+)*(.[a-z]+)?$~';

    case DEFAULT_APP_LANGUAGE = 'en';
    case CS_APP_LANGUAGE = 'cs';
}
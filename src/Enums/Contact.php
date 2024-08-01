<?php declare(strict_types=1);
namespace App\Enums;

enum Contact: string
{
    const NAME_COLUMN_LENGTH = 50;
    const SUBJECT_COLUMN_LENGTH = 100;
    const MESSAGE_COLUMN_LENGTH = 500;
}

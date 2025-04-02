<?php

namespace App\enums;

/**
 * Enum for representing user roles.
 */
enum Role: string
{
    case ADMIN = 'Admin';
    case REGULAR = 'Regular';
}
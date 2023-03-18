<?php

namespace App\Enums;

enum Roles : string
{
    case ADMIN = 'admin';
    case PROFESSOR = 'professor';
    case STUDENT = 'student';
}

<?php

namespace App\Enums;

enum AuthenticationMethods : string {
    case Email = 'email';
    case Phone = 'phone';
    case Username = 'username';
}
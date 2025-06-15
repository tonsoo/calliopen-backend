<?php

namespace App\Exceptions\Authentication;

use Exception;
use Throwable;

class InvalidLoginException extends Exception {
    function __construct() {
        parent::__construct('The login was invalid');
    }
}
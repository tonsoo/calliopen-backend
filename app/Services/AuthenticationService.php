<?php

namespace App\Services;

use App\Enums\AuthenticationMethods;
use App\Exceptions\Authentication\InvalidLoginException;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class AuthenticationService {
    public function authenticate(string $login, string $password) : ?Client {
        $method = AuthenticationMethods::Username;
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $method = AuthenticationMethods::Email;
        }

        $client = Client::where($method->value, $login)->firstOrFail();
        if (!$client || !Hash::check($password, $client?->password)) {
            throw new InvalidLoginException();
        }

        return $client;
    }
}
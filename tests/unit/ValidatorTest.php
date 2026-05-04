<?php

use App\Core\Validator;

$errors = Validator::validate([
    'email' => 'bad-mail',
    'password' => '123',
], [
    'email' => ['required', 'email'],
    'password' => ['required', 'min:8'],
]);

test_assert(isset($errors['email']), 'Validator should reject invalid emails.');
test_assert(isset($errors['password']), 'Validator should reject short passwords.');

$valid = Validator::validate([
    'email' => 'user@example.com',
    'password' => 'VeryStrong1',
], [
    'email' => ['required', 'email'],
    'password' => ['required', 'min:8'],
]);

test_assert($valid === [], 'Validator should accept valid payloads.');

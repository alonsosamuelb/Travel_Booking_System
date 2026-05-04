<?php

namespace App\Core;

class Validator
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleList) {
            $value = trim((string) ($data[$field] ?? ''));

            foreach ($ruleList as $rule) {
                [$name, $parameter] = array_pad(explode(':', $rule, 2), 2, null);

                if ($name === 'required' && $value === '') {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                    break;
                }

                if ($value === '') {
                    continue;
                }

                if ($name === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'Enter a valid email address.';
                    break;
                }

                if ($name === 'min' && mb_strlen($value) < (int) $parameter) {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at least {$parameter} characters.";
                    break;
                }

                if ($name === 'max' && mb_strlen($value) > (int) $parameter) {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must not exceed {$parameter} characters.";
                    break;
                }

                if ($name === 'integer' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be an integer.';
                    break;
                }

                if ($name === 'datetime' && strtotime($value) === false) {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be a valid date and time.';
                    break;
                }

                if ($name === 'in') {
                    $allowed = explode(',', (string) $parameter);
                    if (!in_array($value, $allowed, true)) {
                        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' has an invalid value.';
                        break;
                    }
                }
            }
        }

        return $errors;
    }
}

<?php

use App\Core\Env;

putenv('APP_TEST_FLAG=true');
$_ENV['APP_TEST_FLAG'] = 'true';

test_assert(Env::get('APP_TEST_FLAG') === true, 'Env should normalize boolean true.');
test_assert(Env::get('MISSING_VALUE', 'fallback') === 'fallback', 'Env should return fallback defaults.');

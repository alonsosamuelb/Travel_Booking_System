<?php

use App\Core\Request;

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = ['_method' => 'DELETE', 'name' => 'demo'];
$_GET = [];

test_assert(Request::method() === 'DELETE', 'Request should honor method override.');
test_assert(Request::input('name') === 'demo', 'Request should read POST values.');

$_SERVER['REQUEST_METHOD'] = 'GET';
$_POST = [];
$_GET = ['page' => '3'];

test_assert(Request::method() === 'GET', 'Request should return GET when no override is present.');
test_assert(Request::input('page') === '3', 'Request should read query values.');

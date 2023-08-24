<?php

$routes = [
    'GET'   => [
        '/users' => 'Index/index',
        '/users/creator-applies/configs' => 'CreatorApplies/configs',
        '/users/creator-applies' => 'CreatorApplies/getDetail',
    ],
    'POST'  => [
        '/users/creator-applies' => 'CreatorApplies/create',
    ]
];
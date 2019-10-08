<?php
    $routes = [
        'GET' => [
            'home' => [
                'uri' => '/',
                'action' => 'HomeController@index'
            ],
            '404' => [
                'uri' => '/404',
                'action' => 'AbortController@a_404'
            ]
        ]
    ];

    return $routes;
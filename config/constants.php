<?php

return [
    'limit' => 15,
    'radius_circle' =>3959, //Use 3959 (in miles) or 6371 (in km)
    'images' => [
        'user' => '/images/users/',
        'default' => 'images/default.jpg',
    ],
    'status' => [
        'OK' => 200
    ],
    'social' => [
        'fbAppId' => '1690013057727614',
    ],
    'notifications' =>
        [
            1 => ['title' => 'general', 'msg' => ''],
            2 => ['title' => 'is_match', 'msg' => 'You have received a new match'],
            3 => ['title' => 'is_boost', 'msg' => 'Someone Boost You'],
            4 => ['title' => 'like', 'msg' => 'Someone Like You']
        ],
    'global' => [
        'site' => [
            'name' => 'Laravel Setup',
            'version' => '1.0', // For internal code comparison (if any)
        ],
    ],
    // Directory Constants
    'front' => [
        'default' => [
            'profilePic' => 'default.jpg',
            'profileLoginPic' => 'user.png',
            'profileRoundPic' => 'user.png',
            'siteSetting' => 'images/sitesettings/',
            'adminDP' => 'images/'
        ],
    ],

];

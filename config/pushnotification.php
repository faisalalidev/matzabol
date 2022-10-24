<?php

return [
    'gcm' => [
        'priority' => 'normal',
        'dry_run'  => false,
        'apiKey'   => 'My_ApiKey',
    ],
    'fcm' => [
        'priority' => 'normal',
        'dry_run'  => false,
        'apiKey'   => 'My_ApiKey',
    ],
    'apn' => [
        'certificate' => __DIR__ . '/iosCertificates/Veil_Dis_Push.pem',
        //'certificate' => __DIR__ . '/iosCertificates/Veil_Dev_Push.pem',
        'passPhrase'  => '1', //Optional
        /* 'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional*/
        //TODO:Donot comment this line make it true or false as per your need !!
        //'dry_run'     => true
        'dry_run'     => false
    ]
];
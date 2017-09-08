<?php
return [
    "db"=>[
        "host"=>"127.0.0.1",
        "username"=>"root",
        "password"=>"root",
        "dbname"=>"yii_quanshen_archive",
        "port"=>3306,
        "charset"=>"utf8",
        "prefix"=>""
    ],
    "default"=>[
        "plantform"=>"Admin",
        "controller"=>"Admin",
        "action"=>"index"
    ],
    "upload"=>[
        'max_size' => 1024 * 1024 * 2,
        'allow_types' => ['image/jpeg','image/gif','image/png']
    ]

];

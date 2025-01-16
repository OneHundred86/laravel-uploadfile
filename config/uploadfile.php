<?php

return [
    'allow_exts' => [
        'jpg',
        'png',
        'mp4',
        'mov',
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
    ],

    'view_file_uri' => 'file/view',

    'routes' => [
        [
            'method' => 'post',
            'uri' => 'file/upload',
            'action' => [\Oh86\UploadFile\Controllers\FileController::class, 'upload'],
            'middlewares' => [],
        ],
        [
            'method' => 'get',
            'uri' => 'file/view',
            'action' => [\Oh86\UploadFile\Controllers\FileController::class, 'view'],
            'middlewares' => [],
        ],
        [
            'method' => 'get',
            'uri' => 'file/download',
            'action' => [\Oh86\UploadFile\Controllers\FileController::class, 'download'],
            'middlewares' => [],
        ]
    ],
];
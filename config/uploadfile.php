<?php

return [
    // 允许上传的文件类型（真正文件类型）
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

    // 加密机
    'encryptor' => env('UPLOADFILE_ENCRYPTOR', 'local'),

    // 配置查看文件路由路径
    'app_url' => env('UPLOADFILE_APP_URL', env('APP_URL')),
    'view_file_uri' => 'file/view',
    'tmp_view_file_uri' => 'file/tmp/view',

    // 按业务需求暴露路由路径
    'routes' => [
        [
            'method' => 'post',
            'uri' => 'file/upload',
            'action' => [\Oh86\UploadFile\Controllers\FileController::class, 'upload'],
            'middlewares' => [],
        ],
        [
            'method' => 'get',
            'uri' => 'file/tmp/view',
            'action' => [\Oh86\UploadFile\Controllers\FileController::class, 'tmpView'],
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
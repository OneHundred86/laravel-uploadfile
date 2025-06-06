### 基于laravel框架实现的文件上传/下载/查看功能

#### 1.安装
```shell
composer require oh86/laravel-uploadfile
php artisan vendor:publish --provider="Oh86\UploadFile\UploadFileServiceProvider"

php artisan migrate
```

#### 2.配置`config/uploadfile.php`
```php
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
```

#### 3.文件常见操作
```php
use Oh86\UploadFile\Models\File;

// 上传本地文件
$file = File::upload(new \SplFileInfo('/xxx/target_file.txt'));

// 获取查看url
$url = $file->genTmpViewFileUrl();
$url = $file->genViewFileUrl();

// 标记关联资源
$file->setAssocInfo(\App\Models\Post::class, 1);

// 判断文件类型
$file->isImage();
$file->isVideo();
$file->idPdf();
$file->isWord();
$file->isExcel();
```
<?php

namespace Oh86\UploadFile\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Oh86\SmCryptor\Facades\Cryptor;

/**
 * @property string $id
 * @property string $name
 * @property string $path
 * @property string $mime_type
 * @property string $ext
 * @property int $size
 * @property string $storage
 * @property string $content
 */
class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = false;
    protected $visible = ['id', 'name'];

    /**
     * @param \SplFileInfo | \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param null|string $storePathPrefix
     * @param null|string $storage
     * @return File
     */
    public static function upload($file, $storePathPrefix = null, $storage = null)
    {
        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            $name = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $content = $file->getContent();
            $ext = $file->guessExtension();
        } elseif ($file instanceof \SplFileInfo) {
            $name = $file->getBasename();
            $mimeType = \Illuminate\Support\Facades\File::mimeType($file->getRealPath());
            $content = file_get_contents($file->getRealPath());
            $ext = \Illuminate\Support\Facades\File::guessExtension($file->getRealPath());
        }

        $storage = $storage ?: Storage::getDefaultDriver();
        $path = rtrim($storePathPrefix, '/') . sprintf(
            '/%s/%s',
            date('Ymd'),
            $ext ? sm3($content) . ".$ext" : sm3($content),
        );
        $path = ltrim($path, '/');
        Storage::disk($storage)->put($path, $content);

        return File::query()->create([
            'id' => Str::random(20),
            'name' => $name,
            'path' => $path,
            'mime_type' => $mimeType,
            'ext' => $ext,
            'size' => $file->getSize(),
            'storage' => $storage,
        ]);
    }

    public static function genViewFileSignature(string $fileId, int $expiredAt, string $random)
    {
        return Cryptor::hmacSm3(sprintf('%s%s%s', $fileId, $expiredAt, $random));
    }

    public function getContentAttribute()
    {
        return Storage::disk($this->storage)->get($this->path);
    }

    public function genViewFileUrl(int $validSeconds = 3600)
    {
        $random = Str::random(8);
        $expiredAt = time() + $validSeconds;

        return sprintf(
            '%s/%s?%s',
            config('uploadfile.app_url'),
            ltrim(config('uploadfile.view_file_uri'), '/'),
            http_build_query([
                'id' => $this->id,
                'expiredAt' => $expiredAt,
                'random' => $random,
                'sign' => static::genViewFileSignature($this->id, $expiredAt, $random),
            ])
        );
    }

    /**
     * 设置关联资源信息
     */
    public function setAssocInfo(string $type, int $id)
    {
        $this->update([
            'assoc_type' => $type,
            'assoc_id' => $id,
        ]);
    }

    public function isImage($exts = ['jpg', 'png', 'gif', 'webp']): bool
    {
        return in_array($this->ext, $exts);
    }

    public function isVideo($exts = ['mp4', 'mov', 'webm']): bool
    {
        return in_array($this->ext, $exts);
    }

    public function isWord($exts = ['doc', 'docx']): bool
    {
        return in_array($this->ext, $exts);
    }

    public function isExcel($exts = ['xls', 'xlsx']): bool
    {
        return in_array($this->ext, $exts);
    }

    public function isPdf($exts = ['pdf']): bool
    {
        return in_array($this->ext, $exts);
    }
}

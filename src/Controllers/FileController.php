<?php

namespace Oh86\UploadFile\Controllers;

use Oh86\UploadFile\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Oh86\Http\Response\OkResponse;
use Oh86\Http\Exceptions\ErrorCodeException;

class FileController
{
    public function upload(Request $request)
    {
        $request->validate([
            "file" => "required|file",
        ]);

        $uploadFile = $request->file("file");

        $this->checkUploadFile($uploadFile);
        $file = File::upload($uploadFile, 'uploadFiles');

        return new OkResponse($file);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @throws \Oh86\Http\Exceptions\ErrorCodeException
     */
    protected function checkUploadFile($file)
    {
        if (!in_array($file->guessExtension(), config('uploadfile.allow_exts'))) {
            throw new ErrorCodeException(403, '不允许上传该文件类型', null, 403);
        }
    }

    public function view(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $file = File::findOrFail($request->id);

        return new Response(
            $file->content,
            200,
            [
                'content-type' => $file->mime_type,
            ]
        );
    }

    public function tmpView(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'expiredAt' => 'required|int',
            'random' => 'required',
            'sign' => 'required',
        ]);

        throw_if(
            now()->getTimestamp() > $request->expiredAt,
            new ErrorCodeException(403, '已过期', null, 403)
        );

        throw_if(
            File::genViewFileSignature($request->id, $request->expiredAt, $request->random) != $request->sign,
            new ErrorCodeException(403, '签名错误', null, 403),
        );

        $file = File::findOrFail($request->id);

        return new Response(
            $file->content,
            200,
            [
                'content-type' => $file->mime_type,
            ]
        );
    }

    public function download(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $file = File::findOrFail($request->id);

        return response()->streamDownload(function () use ($file) {
            echo $file->content;
        }, $file->name);
    }
}
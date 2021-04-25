<?php

namespace Haxibiao\Store\Traits;

use Haxibiao\Media\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait ProductRepo
{
    //nova 视频上传
    public function saveVideoFile(UploadedFile $file)
    {
        $hash  = md5_file($file->getRealPath());
        $video = Video::firstOrNew([
            'hash' => $hash,
        ]);
        //        秒传
        if (isset($video->id)) {
            return $video->id;
        }

        $uploadSuccess = $video->saveFile($file);
        throw_if(!$uploadSuccess, Exception::class, '视频上传失败，请联系管理员小哥');
        return $video->id;
    }

    public function saveDownloadImage($file)
    {
        if ($file) {
            $path = 'storage/app-' . env('APP_NAME') . '/products/' . $this->id . '_' . time() . '.png';
            Storage::put($path, file_get_contents($file->path()));
            return $path;
        }
    }
}

<?php

namespace Haxibiao\Store\Traits;

use Illuminate\Support\Facades\Storage;

trait StoreRepo
{

    public function saveDownloadImage($file)
    {
        if ($file) {
            $path = 'storage/app-' . env('APP_NAME') . '/stores/' . $this->id . '_' . time() . '.png';
            Storage::put($path, file_get_contents($file->path()));
            return $path;
        }
    }
}

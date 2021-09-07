<?php

namespace Haxibiao\Store;

use Haxibiao\Breeze\Model;
use Haxibiao\Breeze\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicianProfile extends Model
{
    //空闲
    const FREE_STATUS = 2;
    //工作中
    const WORK_STATUS = 1;
    //没有上班
    const NOT_WORK_STATUS = 0;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //用户状态：参考qq、微信状态
    public static function getStatus()
    {
        return [
            self::FREE_STATUS     => "空闲中",
            self::WORK_STATUS     => "工作中",
            self::NOT_WORK_STATUS => "未上班",
        ];
    }
}

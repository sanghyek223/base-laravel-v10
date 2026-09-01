<?php

namespace App\Models;

use App\Services\CommonServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailFile extends Model
{
    use HasFactory;

    protected $primaryKey = 'sid';

    protected $guarded = [];

    protected $casts = [

    ];

    protected static function booted()
    {
        parent::boot();

        static::deleting(function ($file) {
            // 파일 데이터 삭제시 파일경로에 있는 실제 파일 삭제
            (new CommonServices())->fileDeleteService($file->realfile);
        });
    }

    private function firstSet($data)
    {
        if (!$this->sid) {
            $this->ml_sid = $data['ml_sid'];
        }
    }

    public function setByData($data, $ml_sid)
    {
        $this->firstSet($data);

        $this->realfile = $data['realfile'];
        $this->filename = $data['filename'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'u_sid');
    }

    public function mail()
    {
        return $this->belongsTo(MailList::class, 'ml_sid');
    }

    public function downloadUrl()
    {
        /*
         type => only: 단일, zip: 일괄다운(zip),
         case => switch 문 구분값,
         sid => 키값 enCryptString(sid) 로 암호화해서 전송
        */

        $type = 'only';
        $case = 'mail';
        $sid = enCryptString($this->sid);

        return url("common/download/{$type}/{$case}/{$sid}");
    }
}

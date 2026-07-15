<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailAddress extends Model
{
    use HasFactory;

    protected $primaryKey = 'sid';

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        parent::boot();

        static::deleting(function ($address) {
            // 등록된 주소록 목록 삭제
            $address->list()->delete();
        });
    }

    public function setByData($data)
    {
        $this->title = $data['title'];
    }

    public function list()
    {
        return $this->hasMany(MailAddressDetail::class, 'ma_sid');
    }
}

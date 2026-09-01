<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardReplyCounter extends Model
{
    use HasFactory;

    protected $primaryKey = 'sid';

    protected $guarded = [];

    protected $casts = [

    ];

    public function setByData($data)
    {
        $this->br_sid = $data['sid'];
        $this->ip = request()->ip();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailAddressDetail extends Model
{
    use HasFactory;


    protected $primaryKey = 'sid';

    protected $guarded = [];

    protected $casts = [

    ];

    private function firstSet($data)
    {
        if (!$this->sid) {
            $this->ma_sid = $data['ma_sid'];
        }
    }

    public function setByData($data)
    {
        $this->firstSet($data);

        $this->name = trim($data['name']);
        $this->email = trim($data['email']);
        $this->mobile = trim($data['mobile']);
    }

    public function address()
    {
        return $this->belongsTo(MailAddress::class, 'ma_sid');
    }
}

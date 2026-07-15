<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use HasFactory;

    protected $primaryKey = 'sid';

    protected $guarded = [];

    public function setByData()
    {
        $this->lang = 'ko';
        $this->week = Carbon::today()->dayOfWeek;
    }
}

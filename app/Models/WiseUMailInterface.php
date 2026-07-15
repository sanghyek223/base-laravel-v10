<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WiseUMailInterface extends Model
{
    use HasFactory;

    protected $connection = 'wiseu';
    protected $table = 'NVREALTIMEACCEPT';
    protected $primaryKey = null;

    public $incrementing = false;
    public $timestamps = false;
}

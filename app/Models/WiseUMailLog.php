<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WiseUMailLog extends Model
{
    use HasFactory;

    protected $connection = 'wiseu';
    protected $table = 'NVECARESENDLOG';
    protected $primaryKey = null;

    public $incrementing = false;
    public $timestamps = false;
}

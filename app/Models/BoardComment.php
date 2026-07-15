<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class BoardComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'sid';

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted()
    {
        parent::boot();
    }

    public function setByData($data)
    {
        if (empty($this->sid)) {
            $this->b_sid = $data['b_sid'];
            $this->u_sid = $data['u_sid'] ?? thisPK();
            $this->depth1 = $data['depth1'] ?? 0;
            $this->depth2 = $data['depth2'] ?? 0;
            $this->thread = 0;

            if (!empty($this->depth1)) {
                $this->thread = 1;
            }

            if (!empty($this->depth2)) {
                $this->thread = 2;
            }
        }

        $this->writer = $data['comment_writer'];
        $this->comment = $data['comment'];
    }

    public function user()
    {
        return $this->beLongsTo(User::class, 'u_sid')->withTrashed();
    }

    public function board()
    {
        return $this->beLongsTo(Board::class, 'b_sid');
    }

    public function commentDepth1()
    {
        return self::withTrashed()->where([
            'b_sid' => $this->b_sid,
            'depth1' => $this->sid,
            'depth2' => 0,
        ])->get();
    }

    public function commentDepth2()
    {
        return self::withTrashed()->where([
            'b_sid' => $this->b_sid,
            'depth1' => $this->depth1,
            'depth2' => $this->sid,
        ])->get();
    }
}

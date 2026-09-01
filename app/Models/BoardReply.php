<?php

namespace App\Models;

use App\Services\CommonServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class BoardReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'sid';

    protected $guarded = [];

    protected $casts = [

    ];

    protected static function booted()
    {
        parent::boot();

        static::deleting(function ($reply) {
            // 첨부파일 (plupload) 있을경우 하나씩 삭제
            $reply->files()->each(function ($file) {
                $file->delete();
            });
        });

        static::saved(function ($reply) {
            request()->merge(['br_sid' => $reply->sid]);

            $data = request();
            $plupload_file = $data->plupload_file ?? [];
            $plupload_file_del = $data->plupload_file_del ?? [];

            /* 첨부파일 (plupload) */
            if (!empty($plupload_file)) {
                foreach (json_decode($plupload_file, true) as $row) { // 첨부파일 (plupload) 등록
                    $row['br_sid'] = $reply->sid;

                    $file = new BoardReplyFile();
                    $file->setByData($row);
                    $file->save();
                }
            }

            // 첨부파일 (plupload) 삭제
            if (!empty($plupload_file_del)) {
                foreach ($reply->files()->whereIn('sid', $plupload_file_del)->get() as $plFile) {
                    $plFile->delete();
                }
            }
        });
    }

    protected function boardConfig($code)
    {
        if (empty($code)) {
            $code = request()->code;
        }

        return config("site.board.{$code}");
    }

    public function getBoardConfig($code = '')
    {
        return $this->boardConfig($code);
    }

    public function firstSet($data)
    {
        if (!$this->sid) {
            $this->b_sid = $data['b_sid'];
            $this->u_sid = $data['u_sid'] ?? thisPK();
        }
    }

    public function setByData($data)
    {
        $boardConfig = $this->getBoardConfig();

        $this->firstSet($data);

        $this->writer = $data['writer'] ?? null;
        $this->email = $data['email'] ?? null;

        $this->subject = $data['subject'];
        $this->contents = $data['contents'] ?? null;
        $this->link_url = $data['link_url'] ?? null;
    }

    public function user()
    {
        return $this->beLongsTo(User::class, 'u_sid')->withTrashed();
    }

    public function board()
    {
        return $this->beLongsTo(Board::class, 'b_sid');
    }

    public function files()
    {
        return $this->hasMany(BoardReplyFile::class, 'br_sid');
    }

    public function plDownloadUrl() // 게시판 plupload 전체 파일 다운로드
    {
        switch ($this->files()->count()) {
            case 0: // 파일이 없을경우 (그럴일 없겠지만 혹시나)
                return 'javascript:void(0);';

            case 1: // 게시판 plupload 파일이 하나일 경우 파일만 다운로드
                return $this->files[0]->download();

            default: // 게시판 plupload 파일이 여러개일 경우 압축 파일로 다운로드

                /*
                 type => only: 단일, zip: 일괄다운(zip),
                 case => switch 문 구분값,
                 sid => 키값 enCryptString(sid) 로 암호화해서 전송
                */

                $type = 'zip';
                $case = 'reply';
                $sid = enCryptString($this->sid);

                return url("common/download/{$type}/{$case}/{$sid}");
        }
    }

    public function isNew($hour = 168) // 기본 168 시간(7일) or 변수시간 기준으로 신규게시글 체크
    {
        return (now() <= $this->created_at->addHour($hour)) ? '<span class="ic-new">N</span>' : '';
    }
}

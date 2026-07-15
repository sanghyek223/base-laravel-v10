<?php

namespace App\Models;

use App\Services\CommonServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardReply extends Model
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

        static::deleting(function ($reply) {
            // 첨부파일 (plupload) 있을경우 하나씩 삭제
            $reply->files()->each(function ($file) {
                $file->delete();
            });
        });

        static::saved(function ($reply) {
            $data = request();

            $br_sid = $reply->sid;
            $plupload_file = $data->plupload_file ?? [];
            $plupload_file_del = $data->plupload_file_del ?? [];

            /* 첨부파일 (plupload) */
            if (!empty($plupload_file)) {
                foreach (json_decode($plupload_file, true) as $row) { // 첨부파일 (plupload) 등록
                    $file = new BoardReplyFile();
                    $file->setByData($row, $br_sid);
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

    protected static function boardConfig($code = null)
    {
        return getConfig("board")[$code ?? request()->code];
    }

    public function setByData($data)
    {
        $boardConfig = self::boardConfig($data['code']);

        if (empty($this->sid)) {
            $this->b_sid = $data['b_sid'];
            $this->u_sid = $data['u_sid'] ?? thisPK();
        }

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
                // 관리자 경로로 셋팅될때 있어서 수동으로

                /*
                 'type' => 'zip',
                 'tbl' => 'boardReply',
                 'sid' => enCryptString($this->sid),
                */

                return url('common/fileDownload/zip/boardReply/' . enCryptString($this->sid));
        }
    }

    public function isNew($hour = 168) // 기본 168 시간(7일) or 변수시간 기준으로 신규게시글 체크
    {
        return (now() <= $this->created_at->addHour($hour)) ? '<span class="ic-new">N</span>' : '';
    }
}

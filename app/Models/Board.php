<?php

namespace App\Models;

use App\Services\CommonServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'sid';

    protected $guarded = [];

    protected $casts = [

    ];

    protected static function booted()
    {
        parent::boot();

        static::deleting(function ($board) {
            $board->popups()->delete();

            // 첨부파일 (plupload) 있을경우 하나씩 삭제
            $board->files()->each(function ($file) {
                $file->delete();
            });

            // 썸네일 있을경우 경로에 있는 실제 파일 삭제
            if (!is_null($board->thumbnail_realfile)) {
                (new CommonServices())->fileDeleteService($board->thumbnail_realfile);
            }

            // 게시판 데이터 삭제시 첨부파일(단일파일) 있을경우 경로에 있는 실제 파일 삭제
            foreach(self::boardConfig()['file'] as $key => $val) {
                $pathField = 'realfile' . $key; // 파일 경로 데이터 저장 컬럼

                if (!empty($board->{$pathField})) {
                    (new CommonServices())->fileDeleteService($board->{$pathField});
                }
            }
        });

        static::saved(function ($board) {
            $data = request();

            $b_sid = $board->sid;
            $plupload_file = $data->plupload_file ?? [];
            $plupload_file_del = $data->plupload_file_del ?? [];

            // 팝업 수정 정보 있을때만
            if (!empty($data->popup)) {
                /* popup */
                $popup = $board->popups; // 기존 팝업 불러오기

                /* 팝업 사용시 정보 생성 */
                if ($board->popup === 'Y') {
                    if (is_null($popup)) {
                        // 팝업 정보 없을경우 생성
                        $popup = new BoardPopup();
                        $popup->setByData($data, $b_sid);
                        $popup->save();
                    } else {
                        // 팝업 정보 있을경우 업데이트
                        $popup->setByData($data, $b_sid);
                        $popup->update();
                    }
                } else {
                    if (!is_null($popup)) {
                        // 팝업 설정 아닐경우 팝업 데이터 있으면 삭제
                        $popup->delete();
                    }
                }
            }

            /* 첨부파일 (plupload) */
            if (!empty($plupload_file)) {
                foreach (json_decode($plupload_file, true) as $row) { // 첨부파일 (plupload) 등록
                    $file = new BoardFile();
                    $file->setByData($row, $b_sid);
                    $file->save();
                }
            }

            // 첨부파일 (plupload) 삭제
            if (!empty($plupload_file_del)) {
                foreach ($board->files()->whereIn('sid', $plupload_file_del)->get() as $plFile) {
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
            $this->code = $data['code'];
            $this->u_sid = $data['u_sid'] ?? thisPK();
        }

        $this->writer = $data['writer'] ?? null;
        $this->email = $data['email'] ?? null;

        $this->gubun = $data['gubun'] ?? null;
        $this->category = $data['category'] ?? null;
        $this->subject = $data['subject'];
        $this->contents = $data['contents'] ?? null;
        $this->link_url = $data['link_url'] ?? null;
        $this->notice_sDate = $data['notice_sDate'] ?? null;
        $this->notice_eDate = $data['notice_eDate'] ?? null;
        $this->date_type = $data['date_type'] ?? 'D';

        $this->event_sDate = $data['event_sDate'] ?? null;
        $this->event_eDate = ($this->date_type == 'D') ? null : ($data['event_eDate'] ?? null);

        $this->place = $data['place'] ?? null;
        $this->notice = $data['notice'] ?? 'N';
        $this->popup = $data['popup'] ?? 'N';
        $this->main = $data['main'] ?? 'N';
        $this->hide = $data['hide'] ?? 'N';
        $this->secret = $data['secret'] ?? 'N';

        /* 첨부파일 업로드 or 삭제 */
        if ($data instanceof \Illuminate\Http\Request) { // $data 가 Request 객체일때만.
            foreach($boardConfig['file'] as $key => $val) {
                $file = $data->file("file" . $key) ?? null; // 첨부파일
                $fileDel = $data->{"file" . $key . '_del'} ?? ''; // 파일삭제
                $pathField = 'realfile' . $key; // 파일 경로 데이터 저장 컬럼
                $nameField = 'filename' . $key; // 파일 이름 데이터 저장 컬럼

                // 파일 삭제이면서 기존 첨부파일 있을경우 경로에 있는 실제 파일 삭제
                if (($fileDel == 'Y') && !is_null($this->{$pathField})) {
                    (new CommonServices())->fileDeleteService($this->{$pathField});

                    // 첨부파일이 없다면 기존 파일경로 및 파일명 초기화
                    if (is_null($file)) {
                        $this->{$pathField} = null;
                        $this->{$nameField} = null;
                    }
                }

                // 첨부파일 있을경우 업로드후 경로 저장
                if ($file) {
                    $directory = $boardConfig['directory'];
                    $uploadFile = (new CommonServices())->fileUploadService($file, $directory);
                    $this->{$pathField} = $uploadFile['realfile'];
                    $this->{$nameField} = $uploadFile['filename'];
                }
            }

            /* 썸네일 파일 업로드 or 삭제 */
            $thumbnail = $data->file("thumbnail") ?? null; // 썸네일 첨부파일
            $thumbnailDel = $data->thumbnail_del ?? ''; // 썸네일 파일삭제

            // 파일 삭제이면서 기존 썸네일 있을경우 경로에 있는 실제 파일 삭제
            if (($thumbnailDel == 'Y') && !is_null($this->thumbnail_realfile)) {
                (new CommonServices())->fileDeleteService($this->thumbnail_realfile);

                // 썸네일 없다면 기존 파일경로 및 파일명 초기화
                if (is_null($thumbnail)) {
                    $this->thumbnail_realfile = null;
                    $this->thumbnail_filename = null;
                }
            }

            // 썸네일 있을경우 업로드후 경로 저장
            if ($thumbnail) {
                $directory = $boardConfig['directory'] . '/thumbnail';
                $uploadFile = (new CommonServices())->fileUploadService($thumbnail, $directory);
                $this->thumbnail_realfile = $uploadFile['realfile'];
                $this->thumbnail_filename = $uploadFile['filename'];
            }
        }
    }

    public function user()
    {
        return $this->beLongsTo(User::class, 'u_sid')->withTrashed();
    }

    public function files()
    {
        return $this->hasMany(BoardFile::class, 'b_sid');
    }

    public function popups()
    {
        return $this->hasOne(BoardPopup::class, 'b_sid');
    }

    public function replies()
    {
        return $this->hasMany(BoardReply::class, 'b_sid');
    }

    public function comments()
    {
        return $this->hasMany(BoardComment::class, 'b_sid')->withTrashed();
    }

    public function downloadUrl($field) // 게시판 단일 첨부 파일 다운로드
    {
        /*
         type => only: 단일, zip: 일괄다운(zip),
         case => switch 문 구분값,
         sid => 키값 enCryptString(sid) 로 암호화해서 전송
        */

        $type = 'only';
        $case = 'board';
        $sid = enCryptString($this->sid);

        return url("common/download/{$type}/{$case}/{$sid}?field={$field}");
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
                $case = 'board';
                $sid = enCryptString($this->sid);

                return url("common/download/{$type}/{$case}/{$sid}");
        }
    }

    public function isNew($hour = 168) // 기본 168 시간(7일) or 변수시간 기준으로 신규게시글 체크
    {
        return (now() <= $this->created_at->addHour($hour)) ? '<span class="ic-new">N</span>' : '';
    }

    public function gubunTxt()
    {
        return self::boardConfig()['gubun']['item'][$this->gubun] ?? '';
    }

    public function categoryTxt()
    {
        return self::boardConfig()['category']['item'][$this->category] ?? '';
    }

    public function eventPeriod($yoil = false)
    {
        $sDate = empty($this->event_sDate) ? '' : date('Y.m.d', strtotime($this->event_sDate));

        $txt = $sDate;

        if ($yoil) {
            $sYoil = getYoil($this->event_sDate);
            $txt .= " ({$sYoil})";
        }

        if ($this->date_type === 'L') {
            $eDate = empty($this->event_eDate) ? '' : date('Y.m.d', strtotime($this->event_eDate));

            $txt .= " ~ {$eDate}";

            if ($yoil) {
                $eYoil = getYoil($this->event_eDate);
                $txt .= " ({$eYoil})";
            }
        }

        return $txt;
    }

    public function baseReplySubject()
    {
//        return "[RE] {$this->subject}";
        return $this->subject;
    }

    public function baseReplyContents()
    {
        $baseContents = "<p></p>";
        $baseContents .= "<p></p>";
        $baseContents .= "<p></p>";
        $baseContents .= "<p>========================================</p>";
        $baseContents .= "{$this->user->uid} 님이 쓰신 글";
        $baseContents .= "<p>========================================</p>";
        $baseContents .= $this->contents;

        return $baseContents;
    }
}

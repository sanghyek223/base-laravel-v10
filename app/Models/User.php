<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    const PASSWORD_CHANGE_PERIOD_DAYS = 180; // 비밀번호 변경 주기 (일 기준)

    const PASSWORD_CHANGE_GRACE_MONTHS = 1; // 비밀번호 변경 유예기간 (달 기준)

    protected $primaryKey = 'sid';

    protected $guarded = [];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password_at' => 'datetime',
    ];

    protected static function booted()
    {
        parent::boot();
    }

    protected function userConfig()
    {
        return config('site.user');
    }

    public function getUserConfig()
    {
        return $this->userConfig();
    }

    public function firstSet($data)
    {
        if (!$this->sid) {
            $this->level = $data['level'];
            $this->uid = trim($data['uid']);

            $this->passwordChange($data['password']);
        }
    }

    public function setByData($data)
    {
        $userConfig = $this->getUserConfig();

        $this->firstSet($data);

        $this->name_kr = trim($data['name_kr']);
        $this->email = trim($data['email']);
        $this->mobile = $data['mobile'];
    }

    public function passwordHash($password) // 비밀번호 매칭 체크
    {
        $password = trim($password);
        return Hash::check($password, $this->password);
    }

    public function passwordChange($password, $temp = false) // 비밀번호 변경
    {
        $password = trim($password);
        $imsi = ($temp ? 'Y' : 'N');

        $this->password = Hash::make($password);
        $this->password_at = now();
        $this->imsi_password = $imsi;
    }

    public function makeTempPassword() // 임시 비밀번호 생성
    {
        $n1 = random_int(10, 99);
        $n2 = random_int(10, 99);
        $temp_pw = $n2 . now()->format('is') . $n1;

        return $temp_pw;
    }

    public function setNextPassword() // 비밀번호 변경 주기 유예 주기
    {
        // 현재일 - self::PASSWORD_CHANGE_GRACE_MONTHS
        $this->password_at = now()->subMonths(self::PASSWORD_CHANGE_GRACE_MONTHS);
    }

    public function isPasswordExpired() // 비밀번호 변경 주기 체크 (변경 필요: false, 유지: true)
    {
        // password_at + self::PASSWORD_CHANGE_PERIOD_DAYS < 현재시간
        return $this->password_at->addDays(self::PASSWORD_CHANGE_PERIOD_DAYS)->isPast();
    }

    public function getLevel()
    {
        $userConfig = $this->getUserConfig();
        return $userConfig['level'][$this->level] ?? '';
    }
}

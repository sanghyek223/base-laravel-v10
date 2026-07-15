<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;

class Referer extends Model
{
    use HasFactory;

    protected $primaryKey = 'sid';

    protected $guarded = [];
    
    private function getKeyWord($request)
    {
        $keyword = [];

        if ($request->header('HTTP_REFERER')) {
            foreach ($request->all() as $key => $val) {
                $keyword[] = $val;
            }
        }

        return implode(', ', $keyword);
    }

    public function setByData()
    {
        $agent = new Agent();
        $request = request();

        $this->lang = 'ko';
        $this->ip = $request->ip();
        $this->u_sid = thisAuth()->check() ? thisPK() : null;
        $this->referer = $request->header('HTTP_REFERER') ?? '직접접속';
        $this->browser = $agent->browser();
        $this->platform = $agent->platform();
        $this->keyword = self::getKeyword($request);
    }
}

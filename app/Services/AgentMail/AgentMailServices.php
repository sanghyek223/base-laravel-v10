<?php

namespace App\Services\AgentMail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class AgentMailServices
 * @package App\Services
 */
class AgentMailServices
{
    private $domain;

    private $header = [
        'Content-Type: application/json; charset=utf-8',
        'Accept: application/json; charset=utf-8',
    ];

    private $basUrl = 'https://wiseuapi2.m2comm.co.kr';

    private $api_url;

    private $access_token = ''; // 학회별 access token

    private $app_name;

    public function __construct()
    {
        $this->domain = env('APP_URL');
        $this->app_name = env('APP_NAME');
        $this->api_url = $this->basUrl . '/api/';
    }

    private function bearerTokenCheck(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $this->makeFailReturnJson('인증 토큰 없음');
        }

        if (base64_decode($token) !== $this->access_token) {
            return $this->makeFailReturnJson('인증 토큰 불일치');
        }

        return $this->makeSucReturnJson();
    }

    private function makeFailReturnJson($msg, $data = null)
    {
        return [
            'status' => 'fail',
            'msg' => $msg,
            'data' => $data ?? [],
        ];
    }

    private function makeSucReturnJson($data = null)
    {
        return [
            'status' => 'suc',
            'msg' => '성공',
            'data' => $data ?? [],
        ];
    }

    private function makeSelectQuery($table, $searchKey)
    {
        /* sid: 키값 name: 수신인 이름, email: 수신이메일, mobile: 휴대폰 (고정된 필수값 키값 변경안됨) */
        /* sid, name, email, mobile 없을경우 select 값에 추가후 쿼리 만들기 */
        /* $searchKey 검색은 실제 이름, 휴대폰, 이메일 필드로 검색 */

        try {
            switch ($table) {
                case 'users':
                    $query = DB::table($table)->selectRaw('*, name_kr AS name')->whereNull('deleted_at');

                    // 검색어 있을경우
                    if (!empty($searchKey)) {
                        $query->where(function ($q) use ($searchKey) {
                            $q->orWhere('name_kr', 'like', "%{$searchKey}%")
                                ->orWhere('email', 'like', "%{$searchKey}%")
                                ->orWhere(function ($qq) use ($searchKey) {
                                    $qq->where('mobile', 'like', "%{$searchKey}%")
                                        ->orWhereRaw("REPLACE(mobile, '-', '') LIKE ?", ["%" . str_replace('-', '', $searchKey) . "%"]);
                                });
                        });
                    }
                    break;

                default:
                    return $this->makeFailReturnJson('지원하지않는 형태');
            }

            return ['query' => $query];
        } catch (\Exception $e) {
            return $this->makeFailReturnJson('Make Select Query ERROR', $e->getMessage());
        }
    }

    private function makeWhereQuery($query, $table, $searchFilter)
    {
        $settingJson = $this->settingJson();
        $filterJson = $settingJson['data']['result'][$table];

        if (empty($filterJson)) {
            return $this->makeFailReturnJson('검색 필터가 잘못 되었습니다.', array(
                'table' => $table,
                'filterJson' => $filterJson,
                'searchFilter' => $searchFilter,
            ));
        }

        try {
            foreach ($filterJson['column'] as $key => $val) {

                $searchKeyword = $searchFilter[$key];

                if (empty($searchKeyword)) {
                    continue;
                }

                // 회원 테이블 특정필드 예외 처리 쿼리 (예시)
                if ($table === 'users' && $key === 'level') {
                    $query->whereIn("level", $searchKeyword);
                    continue;
                }

                switch ($val['html']) {
                    case 'input':
                        $query->where($key, 'like', "%{$searchKeyword}%");
                        break;

                    case 'checkbox':
                        $quoteStringResult = $this->quoteString($searchKeyword);
                        $query->whereIn($key, $quoteStringResult);
                        break;

                    case 'radio':
                    case 'select':
                        $query->where($key, $searchKeyword);
                        break;

                    default:
                        break;
                }
            }

            return ['query' => $query];
        } catch (\Exception $e) {
            return $this->makeFailReturnJson('Make Where Query ERROR', $e->getMessage());
        }
    }

    private function quoteString($searchKeyword)
    {
        $quoted = [];

        foreach ($searchKeyword as $item) {
            $quoted[] = "'" . $item . "'";
        }

        return $quoted;
    }

    private function convertUTF8($variable)
    {
        $result = [];

        foreach ($variable as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->convertUTF8($value);
            } else if (is_string($value)) {
                $encoding = mb_detect_encoding($value, ['EUC-KR', 'UTF-8', 'SJIS', 'ISO-8859-1'], true);

                $result[$key] = ($encoding && $encoding !== 'UTF-8')
                    ? iconv($encoding, 'UTF-8//IGNORE', $value)
                    : $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function dataAction($request)
    {
        $case = $request->case ?? '';

        if ($case == 'api-check') {
            return $this->makeSucReturnJson([
                'result' => "{$this->app_name}  API 요청 성공",
            ]);
        }

        if ($case !== 'auth-login') {
            $checkResult = $this->bearerTokenCheck($request);

            if ($checkResult['status'] !== 'suc') {
                return $checkResult;
            }
        }

        switch ($case) {
            case 'auth-login':
                return $this->authLogin();

            case 'setting-json':
                return $this->settingJson();

            case 'target-paging-list': // paging
                return $this->targetPagingList($request);

            case 'target-all-list': // 전체 발송
                return $this->targetAllList($request);

            case 'target-select-list': // 선택 발송 or 개별 발송
                return $this->targetSelectList($request);

            default:
                return $this->makeFailReturnJson('지원하지않는 형태');
        }
    }

    private function settingJson()
    {
        $userConfig = config('site.user');

        // 검색 테이블
        $json['table'] = [
            'users' => '회원',
        ];

        // 메일 템플릿
        $json['template'] = [
            'T1' => [
                'name' => '템플릿 1',
                'path' => "{$this->domain}/api/mail/template/temp01",
            ],
        ];

        // 테이블별 정보 (개별셋팅)
        $json['users'] = [
            // 치환 정보
            'replace' => [
                '{LICENCE_NUMBER}' => [
                    'name' => '면허번호',
                    'column' => 'license_number',
                ],
            ],

            // 테이블 컬럼 정보
            'column' => [
                'level' => [
                    'name' => '회원등급', // 노출명
                    'data' => $userConfig['level'], // data
                    'html' => 'checkbox', // view 페이지 노출 (input, checkbox, radio, select)
                ],
            ],
        ];

        return $this->makeSucReturnJson([
            'result' => $json ?? [],
        ]);
    }

    private function targetPagingList(Request $request)
    {
        $table = $request['table'];
        $searchFilter = json_decode($request['searchFilter'], true);

        $queryResult = $this->makeSelectQuery($table, $request['searchKey']);

        if (empty($queryResult['query'])) {
            return $queryResult;
        }

        $queryResult = $this->makeWhereQuery($queryResult['query'], $table, $searchFilter);

        if (empty($queryResult['query'])) {
            return $queryResult;
        }

        $query = $queryResult['query'];

        // order by 추가
        switch ($table) {
            case 'users':
                $query->orderBy('name_kr');
                break;

            default:
                return $this->makeFailReturnJson('지원하지않는 형태');
        }

        // 리스트용 (페이징))
        return $this->dataProcessing($query, [
            'page' => $request['page'],
            'paginate' => $request['paginate'],
        ]);
    }

    private function targetAllList(Request $request)
    {
        $table = $request['table'];
        $searchFilter = json_decode($request['searchFilter'], true);

        $queryResult = $this->makeSelectQuery($table, $request['searchKey']);

        if (empty($queryResult['query'])) {
            return $queryResult;
        }

        $queryResult = $this->makeWhereQuery($queryResult['query'], $table, $searchFilter);

        if (empty($queryResult['query'])) {
            return $queryResult;
        }

        $query = $queryResult['query'];

        // 발송용
        return $this->dataProcessing($query);
    }

    private function targetSelectList(Request $request)
    {
        $table = $request['table'];
        $checkedArray = json_decode($request['checkedArray'], true);
        $checkedArray = $this->quoteString($checkedArray);

        $queryResult = $this->makeSelectQuery($table, $request['searchKey']);

        if (empty($queryResult['query'])) {
            return $queryResult;
        }

        $query = $queryResult['query']->whereIn('sid', $checkedArray);

        // 발송용
        return $this->dataProcessing($query);
    }

    private function dataProcessing($query, $pagingData = [])
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $returnData = [];
        $returnData['item'] = [];

        try {
            $totalRecordQuery = clone $query;

            if (empty($pagingData)) {
                // 발송용
                $resultList = $query->get();
            } else {
                // 페이징
                $page = (int)$pagingData['page'];
                $paginate = (int)$pagingData['paginate'];

                $list = $query->paginate($paginate)->appends(['page' => $page]);
                $resultList = collect($list->items());

                // 페이징 정보
                $returnData['pageInfo'] = [
                    'page' => $list->currentPage(), // 현재 페이지 번호
                    'total' => $list->total(), // 총 레코드 수
                    'pages' => $list->lastPage(), // 총 페이지 수 (마지막 페이지)
                ];
            }

            $returnData['total'] = $totalRecordQuery->count();
            $returnData['item'] = $resultList->toArray(); // 배열로 변환

            return $this->makeSucReturnJson(array(
                'query' => $this->queryBinding($query),
                'result' => $returnData,
            ));
        } catch (\Exception $e) {
            return $this->makeFailReturnJson("Query ERROR", array(
                'query' => $this->queryBinding($query),
                'result' => $e->getMessage(),
            ));
        }
    }

    private function queryBinding($query) // 라라벨 전용
    {
        $sql = $query->toSql();
        $bindings = $query->getBindings();

        // 쿼리문 바인딩
        return vsprintf(str_replace('?', '%s', $sql), array_map(function ($binding) {
            // 문자열은 쿼리 내에서 작은 따옴표로 감싸야 함
            return is_numeric($binding) ? $binding : "'" . addslashes($binding) . "'";
        }, $bindings));
    }

    // 메일 서버 로그인
    private function authLogin()
    {
        $response = $this->secretToken();

        // secretToken 발급 실패
        if ($response['code'] != 2000) {
            return $response;
        }

        $secret_token = $response['data']['secret_token'];
        $make_login_url = $this->basUrl . '/' . base64_encode($secret_token) . '/api-login';

        return [
            'url' => $make_login_url
        ];
    }

    // secret token 발급 요청
    private function secretToken()
    {
        $url = $this->api_url . 'auth/' . $this->access_token . '/token';
        return $this->callGetCurl($url);
    }

    // mail 발송 중계서버로 GET 요청
    private function callGetCurl($url)
    {
        // cURL 세션 초기화
        $ch = curl_init();

        // cURL 옵션 설정
        curl_setopt_array($ch, [
            CURLOPT_URL => $url, // 요청 url
            CURLOPT_RETURNTRANSFER => true, // 반환값 사용
            CURLOPT_HTTPHEADER => $this->header,
        ]);

        // 요청 실행
        $response = curl_exec($ch);

        // 에러 체크
        if ($response === false) {
            $response = curl_error($ch);
        }

        // cURL 세션 종료
        curl_close($ch);

        // var_dump($response);
        // exit;

        return json_decode($response, true);
    }

    // mail 발송 중계서버로 POST 요청
    private function callPostCurl($url, $postData = [])
    {
        // cURL 세션 초기화
        $ch = curl_init();

        // cURL 옵션 설정
        curl_setopt_array($ch, [
            CURLOPT_URL => $url, // 요청 url
            CURLOPT_POST => true, // POST 요청
            CURLOPT_RETURNTRANSFER => true, // 반환값 사용
            CURLOPT_POSTFIELDS => json_encode($postData), // post 데이터
            CURLOPT_HTTPHEADER => $this->header,
        ]);

        // 요청 실행
        $response = curl_exec($ch);

        // 에러 체크
        if ($response === false) {
            $response = curl_error($ch);
            var_dump($response);
            exit;
        }

        // cURL 세션 종료
        curl_close($ch);

        if ($_SERVER['REMOTE_ADDR'] == '218.235.94.217') {
//             var_dump($response);
//             exit;
        }

        return json_decode($response, true);
    }
}

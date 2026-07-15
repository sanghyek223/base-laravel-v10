<?php

// check Url
if (!function_exists('checkUrl')) {
    function checkUrl(): string
    {
        $uri = str_replace('://www.', '://', request()->getUri());

        if (str_contains($uri, config('site.app.api.url'))) {
            return 'api';
        }

        if (str_contains($uri, config('site.app.admin.url'))) {
            return 'admin';
        }

        return 'web';
    }
}

// global auth
if (!function_exists('thisAuth')) {
    function thisAuth()
    {
        if (checkUrl() === 'admin') {
            return auth('admin');
        }

        return auth('web');
    }
}

// get App Name
if (!function_exists('getAppName')) {
    function getAppName(): string
    {
        return config('site.app.' . checkUrl() . '.app_name');
    }
}

// get default url
if (!function_exists('getDefaultUrl')) {
    function getDefaultUrl($auth = false): string
    {
        if ($auth) {
            return thisAuth()->check()
                ? getDefaultUrl()
                : url('auth/login');
        }

        return url('/');
    }
}

// thisLevel
if (!function_exists('thisLevel')) {
    function thisLevel(): string
    {
        return thisUser()->level ?? '';
    }
}

// isAdmin
if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return (thisLevel() === 'M');
    }
}

// D-day
if (!function_exists('DDay')) {
    function DDay($date) // $date => Y-m-d 형태
    {
        $currentDate = \Carbon\Carbon::now();

        // 같을때
        if (\Carbon\Carbon::parse($date)->isSameDay($currentDate)) {
            return "D-day";
        }

        // 이전 날짜
        if (\Carbon\Carbon::parse($date)->isBefore($currentDate)) {
            return 'END';
        }

        $date = explode('-', $date);
        $targetDate = \Carbon\Carbon::create($date[0], $date[1], $date[2]);

        $daysUntilTarget = $currentDate->diffInDays($targetDate) + 1;

        if ($daysUntilTarget > 0) {
            return "D-" . $daysUntilTarget;
        }
    }
}

// wiseu connection
if (!function_exists('wiseuConnection')) {
    function wiseuConnection()
    {
        $host = env('DB_HOST_WISEU');
        $port = env('DB_PORT_WISEU', '1433');
        $dbname = env('DB_DATABASE_WISEU');
        $username = env('DB_USERNAME_WISEU');
        $password = env('DB_PASSWORD_WISEU');

        try {
            $conn = new \PDO(
                "dblib:host={$host}:{$port};dbname={$dbname};TrustServerCertificate=True",
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::SQLSRV_ATTR_ENCODING => \PDO::SQLSRV_ENCODING_UTF8
                ]
            );

            return $conn;
        } catch (\PDOException $e) {
            // Log or handle the connection error
            throw $e;
        }
    }
}


@extends('admin.layouts.admin-popup-layout')

@section('addStyle')
    <style>
        body {
            margin: 0;
            padding: 20px;
            height: 100vh;
            overflow: auto;
            background: #f5f5f5;
            font-family: "Malgun Gothic", sans-serif;
            font-size: 13px;
        }

        body table {
            border-collapse: collapse;
            width: max-content;
            min-width: 100%;
            background: #fff;
            border: 2px solid #666;
        }

        /* 공통 */
        body th,
        body td {
            border: 1px solid #999;
            padding: 6px 8px;
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
        }

        /* 헤더 */
        body thead th {
            background: #d9eaf7;
            font-weight: bold;
        }
    </style>
@endsection

@section('contents')
    @php
        $headings = $previewData['headings'];
        $data = $previewData['data'];

        $baseHeading = $headings[0];
        $twoHeading = $headings[1] ?? [];

        $grouped = [];
        $baseGroupKey = null;

        $currentKey = null;
        $rowSpan = count($headings);
        $colSpan = 1;

        foreach ($baseHeading as $key => $val) {
            if (empty($baseHeading[$key + 1])) {
                if (is_null($baseGroupKey)) {
                    $baseGroupKey = $key;
                }

                $colSpan++;
                continue;
            }

            if (!is_null($baseGroupKey)) {
                $grouped[$baseGroupKey] = $colSpan;
                $baseGroupKey = null;
                $colSpan = 1;
            }
        }

        // 마직막 헤딩 데이터
        if (!empty($baseGroupKey)) {
            $grouped[$baseGroupKey] = $colSpan - 1;
        }
    @endphp

    <div class="sub-contents">
        <div class="sub-tit-wrap">
            <h3 class="sub-tit">{{ $fileName }} Excel</h3>
        </div>

        <div class="table-wrap">
            <table class="cst-table" style="table-layout: auto;">
                <thead>
                <tr>
                    @foreach($baseHeading as $index => $item)
                        @empty($item) @continue @endempty

                        @if(!empty($twoHeading) && empty($twoHeading[$index]))
                            <th style="text-align: center;" rowspan="{{ $rowSpan }}">{{ $item }}</th>
                            @continue
                        @endif

                        <th style="text-align: center;" colspan="{{ $grouped[$index] ?? 1 }}">{{ $item }}</th>
                    @endforeach
                </tr>

                @if(!empty($twoHeading))
                    <tr>
                        @foreach($twoHeading as $index => $item)
                            @empty ($item) @continue @endempty
                            <th style="text-align: center;" >{{ $item }}</th>
                        @endforeach
                    </tr>
                @endif
                </thead>

                <tbody>
                @foreach($data as $key => $val)
                    <tr>
                        @foreach($val as $index => $item)
                            <td style="width: 200px;">{!! nl2br($item) !!}</td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('addScript')
@endsection

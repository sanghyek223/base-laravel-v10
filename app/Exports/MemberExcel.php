<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Database\Eloquent\Builder;

class MemberExcel implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithEvents, WithChunkReading
{
    private $userConfig;
    private $query;
    private $total;
    private $row = 0;

    public function __construct($data)
    {
        $this->userConfig = getConfig('user');
        $this->query = $data['query'];
        $this->total = $data['total'];
    }

    // 미리보기용 데이터 반환 (컬렉션 형태)
    public function getPreviewData()
    {
        $previewQuery = clone $this->query;

        $mappedData = [];
        $this->row = 0; // 초기화

        foreach ($previewQuery->get() as $item) {
            $mappedData[] = $this->map($item);
        }

        return [
            'headings' => [
                $this->headings(),
            ],
            'data' => collect($mappedData),
            'total' => $this->total,
        ];
    }

    public function query()
    {
        return $this->query;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function headings(): array
    {
        return [
            'No',
            '회원등급',
            'ID',
            '이름',
            '핸드폰',
            '이메일',
            '가입일',
        ];
    }

    public function map($data): array
    {
        return [
            $this->total - ($this->row++),
            $data->getLevel(),
            $data->uid ?? '',
            $data->name_kr ?? '',
            $data->mobile ?? '',
            $data->email ?? '',
            $data->created_at,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $event->sheet->getStyle("A:ZZ")->getAlignment()->setWrapText(true);
                $event->sheet->getStyle('A:ZZ')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $event->sheet->getStyle('A:ZZ')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $event->sheet->getDelegate()->getStyle('A1:ZZ1')->getFont()->setBold(true)->setSize(12);

                // 컬럼 자동 너비 조정
                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
            },
        ];
    }
}

<div class="table-wrap" style="margin-top: 10px;">
    <table class="cst-table list-table">
        <caption class="hide">목록</caption>

        <colgroup>
            <col style="width: 4%;">
            <col style="width: 5%;">
            <col style="width: 15%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col>
        </colgroup>

        <thead>
        <tr>
            <th scope="col">No</th>
            <th scope="col">회원등급</th>
            <th scope="col">ID</th>
            <th scope="col">이름</th>
            <th scope="col">핸드폰</th>
            <th scope="col">이메일</th>
        </tr>
        </thead>

        <tbody>
        @forelse($list as $row)
            <tr data-sid="{{ $row->sid }}">
                <td>{{ number_format($row->seq) }}</td>
                <td>{{ $row->getLevel() }}</td>
                <td>{{ $row->uid ?? '' }}</td>
                <td>{{ $row->name_kr ?? '' }}</td>
                <td>{{ $row->mobile ?? '' }}</td>
                <td>{{ $row->email ?? '' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6">등록된 회원이 없습니다.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@section('list-script')
@endsection

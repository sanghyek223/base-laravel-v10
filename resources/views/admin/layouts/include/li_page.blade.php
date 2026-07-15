<select id="li_page" class="form-item">
    <option value="20" {{ $li_page == 20 ? 'selected' : '' }}>20</option>
    <option value="30" {{ $li_page == 30 ? 'selected' : '' }}>30</option>
    <option value="50" {{ $li_page == 50 ? 'selected' : '' }}>50</option>
    <option value="100" {{ $li_page == 100 ? 'selected' : '' }}>100</option>
</select>
개씩 보기
<div>
    <header>
        <h2>@lang($this->getAdminConfig()->title)</h2>
    </header>
    <section class="listview">
        <table>
            <tr>
                @foreach($listview as $column)
                    <th>@lang($column)</th>
                @endforeach
            </tr>
            @foreach($module->all($listview) as $row)
                <tr>
                    @foreach($listview as $column) 
                        <td>{{ $row->$column }}</td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </section>
</div>

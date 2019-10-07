<table>
    <thead>
        <tr>
         @foreach($header as $head)
            <th>{{ $head }}</th>
         @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data as $baris)
            <tr>
            @foreach($baris as $item)
                <td>{{ $item }}</td>
            @endforeach    
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
    <tr>
        <th>Code</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td>{{ $item->code }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
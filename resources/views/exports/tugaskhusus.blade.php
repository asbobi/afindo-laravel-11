<table>
    <thead>
    <tr>
        <th>Kode Pegawai</th>
        <th>Tanggal</th>
        <th>Jam Datang</th>
        <th>Jam Pulang</th>
    </tr>
    </thead>
    <tbody>
    @foreach($datas as $data)
        <tr>
            <td>{{ $data->kodepegawai }}</td>
            <td>{{ $data->tanggal }}</td>
            <td>{{ $data->jamdatang }}</td>
            <td>{{ $data->jampulang }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

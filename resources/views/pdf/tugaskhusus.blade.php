<!DOCTYPE html>

<head>
    <style>
        .text-center {
            text-align: center;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0;
        }

        .main-data {
            margin-top: 20px;
        }

        th {
            font-size: 11px;
        }

        .table th,
        .table td {
            padding: 6px;
        }

        .table-bordered td,
        .table-bordered th {
            border: solid 1px #b3b3b3;
        }

        td {
            font-size: 11px;
        }

        .main-tbody td {
            font-size: '11px';
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <div class="main-data">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode Pegawai</th>
                    <th>Tanggal</th>
                    <th>Jam Datang</th>
                    <th>Jam Pulang</th>
                </tr>
            </thead>
            <tbody class="main-tbody">
                @foreach ($datas as $data)
                    <tr>
                        <td>{{ $data->kodepegawai }}</td>
                        <td>{{ $data->tanggal }}</td>
                        <td>{{ $data->jamdatang }}</td>
                        <td>{{ $data->jampulang }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>

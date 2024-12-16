<!DOCTYPE html>
<html>

    <head>
        <style>
            @page {
                size: 330mm 210mm;
                margin: 10mm 15mm 10mm 15mm;
            }

            .packing-list-info {
                width: 100%;
                margin-bottom: 20px;
            }

            .packing-list-info div {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
            }

            .packing-list-info div span {
                display: inline-block;
            }

            .packing-list-info div .value {
                display: inline-block;
                width: 60%;
                text-align: right;
            }

            .packing-list-info h2 {
                text-align: center;
                margin: 0;
            }

            .table-wrapper {
                width: 100%;
            }

            table {
                width: 48%;
                border-collapse: collapse;
                float: left;
                margin-right: 2%;
                vertical-align: top;
            }

            table,
            th,
            td {
                border: 1px solid black;
            }

            th,
            td {
                padding: 8px;
                text-align: left;
            }

            .page-break {
                page-break-before: always;
                clear: both;
            }
        </style>
    </head>

    <body>
        @php
            $isFirstPage = true;
        @endphp

        @foreach ($pagedData as $page)
            @php
                $half = ceil(count($page) / 2);
                $leftData = array_slice($page, 0, $half);
                $rightData = array_slice($page, $half);
            @endphp

            @if ($isFirstPage)
                <div class="packing-list-info">
                    <h2>PACKING LIST KIRIM SERVICE</h2>
                    <div>
                        <span>Hari/Tanggal</span>
                        <span class="value">..........................</span>
                    </div>
                    <div>
                        <span>Nomor Surat Jalan</span>
                        <span class="value">..........................</span>
                    </div>
                    <div>
                        <span>Konsumen</span>
                        <span class="value">..........................</span>
                    </div>
                    <div>
                        <span>Nama Yang Input</span>
                        <span class="value">..........................</span>
                    </div>
                    <div>
                        <span>Jam Pengiriman</span>
                        <span class="value">..........................</span>
                    </div>
                </div>

                @php
                    $isFirstPage = false;
                @endphp
            @endif

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Item Service</th>
                            <th>Kondisi</th>
                            <th>Qty</th>
                            <th>Crosscheck</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leftData as $item)
                            <tr>
                                <td>{{ $item["no"] }}</td>
                                <td>{{ $item["item"] }}</td>
                                <td>{{ $item["kondisi"] }}</td>
                                <td>{{ $item["qty"] }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Item Service</th>
                            <th>Kondisi</th>
                            <th>Qty</th>
                            <th>Crosscheck</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rightData as $item)
                            <tr>
                                <td>{{ $item["no"] }}</td>
                                <td>{{ $item["item"] }}</td>
                                <td>{{ $item["kondisi"] }}</td>
                                <td>{{ $item["qty"] }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="page-break"></div>
        @endforeach
    </body>

</html>

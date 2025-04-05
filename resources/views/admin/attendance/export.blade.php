<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Absensi - {{ now()->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 5mm;
            text-align: center;
        }

        h4 {
            margin-bottom: 8px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 10px;
        }

        th, td {
            border: 0.5px solid #ddd;
            text-align: center;
            vertical-align: middle;
            padding: 3px;
        }

        /* Informasi Karyawan */
        .employee-data {
            font-size: 9px;
            font-weight: bold;
            min-width: 45px;
            max-width: 80px;
        }

        /* Kolom Tanggal */
        .date-cell {
            font-size: 7px !important;
            padding: 2px !important;
            min-width: 10px !important;
            max-width: 12px !important;
            background-color: #f9f9f9;
        }

        .day-name {
            display: block;
            font-size: 6px;
            margin-top: 1px;
        }

        /* Statistik Kehadiran */
        .stats {
            font-weight: bold;
            background-color: #e6e6e6;
        }

        /* Warna Status */
        .status-H { background-color: #dff0d8; }
        .status-T { background-color: #fcf8e3; }
        .status-I { background-color: #d9edf7; }
        .status-S { background-color: #e2d3f5; }
        .status-A { background-color: #f8d7da; }
        .status-L { background-color: #ffcccc; }
    </style>
</head>
<body>
    <h4>Laporan Absensi {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</h4>

    <table>
        <thead>
            <tr>
                <th class="employee-data">Nama</th>
                <th class="employee-data">NIP</th>
                <th class="employee-data">Divisi</th>
                <th class="employee-data">Jabatan</th>

                <!-- Kolom Tanggal -->
                @foreach($dateRange as $day)
                    <th class="date-cell {{ $day['is_weekend'] ? 'weekend' : '' }} {{ $day['is_sunday'] ? 'sunday' : '' }}">
                        {{ Carbon\Carbon::parse($day['date'])->format('d/m') }}
                        <span class="day-name">
                            {{ Carbon\Carbon::parse($day['date'])->isoFormat('dd') }}
                        </span>
                    </th>
                @endforeach

                <th class="employee-data stats">H</th>
                <th class="employee-data stats">T</th>
                <th class="employee-data stats">I</th>
                <th class="employee-data stats">S</th>
                <th class="employee-data stats">A</th>
                <th class="employee-data stats">L</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendanceData as $data)
                <tr>
                    <!-- Data Karyawan -->
                    <td class="employee-data">{{ $data['name'] }}</td>
                    <td class="employee-data">{{ $data['nip'] }}</td>
                    <td class="employee-data">{{ $data['division'] }}</td>
                    <td class="employee-data">{{ $data['position'] }}</td>

                    <!-- Kolom Absensi -->
                    @foreach($data['dates'] as $date => $status)
                        @php
                            $currentDate = Carbon\Carbon::parse($date);
                            $isSunday = $currentDate->isSunday();
                        @endphp
                        <td class="date-cell status-{{ $status }} {{ $isSunday ? 'sunday' : '' }}">
                            {{ $status }}
                        </td>
                    @endforeach

                    <!-- Statistik -->
                    <td class="employee-data stats">{{ $data['stats']['H'] }}</td>
                    <td class="employee-data stats">{{ $data['stats']['T'] }}</td>
                    <td class="employee-data stats">{{ $data['stats']['I'] }}</td>
                    <td class="employee-data stats">{{ $data['stats']['S'] }}</td>
                    <td class="employee-data stats">{{ $data['stats']['A'] }}</td>
                    <td class="employee-data stats">{{ $data['stats']['L'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

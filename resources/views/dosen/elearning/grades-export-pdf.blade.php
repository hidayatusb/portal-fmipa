<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Nilai {{ $course->title }}</title>
    <style>
        @page {
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
            margin: 0;
            padding: 20px;
        }

        h1 {
            font-size: 22px;
            margin: 0 0 6px;
        }

        .meta {
            color: #6b7280;
            margin-bottom: 18px;
            line-height: 1.6;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #f3f4f6;
            font-weight: 700;
            font-size: 11px;
        }

        td.score,
        th.score {
            text-align: center;
            white-space: nowrap;
        }

        tr:nth-child(even) td {
            background: #fafafa;
        }

        .empty {
            margin-top: 24px;
            color: #6b7280;
        }
    </style>
</head>

<body>
    <h1>Rekap Nilai Mahasiswa</h1>
    <div class="meta">
        <div><strong>Mata Kuliah:</strong> {{ $course->title }} ({{ $course->code }})</div>
        <div><strong>Dosen Pengampu:</strong> {{ $course->lecturer->name }}</div>
        <div>
            <strong>Bobot:</strong>
            Kehadiran {{ $course->weight_attendance }}% ·
            Tugas {{ $course->weight_assignment }}% ·
            UTS {{ $course->weight_uts }}% ·
            UAS {{ $course->weight_uas }}%
        </div>
        <div><strong>Diekspor:</strong> {{ $generatedAt->locale('id')->translatedFormat('d F Y, H:i') }}</div>
    </div>

    @if (count($rows) === 0)
        <p class="empty">Belum ada mahasiswa yang terdaftar di kelas ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th class="score">No</th>
                    <th>Nama Mahasiswa</th>
                    <th>Username</th>
                    @foreach ($assignments as $index => $assignment)
                        <th class="score">{{ \App\Services\CourseGradesReport::assignmentHeading($index + 1) }}</th>
                    @endforeach
                    <th class="score">Kehadiran</th>
                    <th class="score">Rata-rata Tugas</th>
                    <th class="score">UTS</th>
                    <th class="score">UAS</th>
                    <th class="score">Nilai Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td class="score">{{ $row['no'] }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ $row['username'] }}</td>
                        @foreach ($assignments as $assignment)
                            <td class="score">
                                {{ \App\Services\CourseGradesReport::scoreLabel($row['scores'][$assignment->id] ?? null) }}
                            </td>
                        @endforeach
                        <td class="score">{{ \App\Services\CourseGradesReport::scoreLabel($row['attendance_score']) }}</td>
                        <td class="score">{{ \App\Services\CourseGradesReport::averageLabel($row['assignment_average']) }}</td>
                        <td class="score">{{ \App\Services\CourseGradesReport::scoreLabel($row['uts_score']) }}</td>
                        <td class="score">{{ \App\Services\CourseGradesReport::scoreLabel($row['uas_score']) }}</td>
                        <td class="score">{{ \App\Services\CourseGradesReport::averageLabel($row['final_grade']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>

</html>

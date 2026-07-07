<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">

        <style>
            body {
                font-family: sans-serif;
                font-size: 10px;
            }

            h2 {
                text-align: center;
                margin-bottom: 4px;
            }

            .date {
                text-align: center;
                margin-bottom: 15px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th {
                background: #f0f0f0;
                font-weight: bold;
            }

            th,
            td {
                border: 1px solid #444;
                padding: 5px;
            }

            .center {
                text-align: center;
            }

            .small {
                font-size: 9px;
            }
        </style>
    </head>

    <body>
        <h2>Reporte de Alimentación</h2>

        <div class="date">
            Semana del {{ $weekStart->format('d/m/Y') }} al {{ $weekEnd->format('d/m/Y') }}
        </div>

        @for ($i = 0; $i < 5; $i++)
            @php
                $currentDate = $weekStart->copy()->addDays($i);
                $dayName = ucfirst($currentDate->locale('es')->translatedFormat('l'));
            @endphp

            <h3>{{ $dayName }} {{ $currentDate->format('d/m/Y') }}</h3>

            <table>
                <thead>
                    <tr>
                        <th>Catálogo</th>
                        <th>Grado</th>
                        <th>Arma</th>
                        <th>Nombre</th>
                        <th>Desayuno</th>
                        <th>Almuerzo</th>
                        <th>Cena</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($records as $record)
                        @php
                            $meal = $record->mealAttendances->first(function ($item) use ($currentDate) {
                                return \Carbon\Carbon::parse($item->date)->toDateString()
                                    === $currentDate->toDateString();
                            });
                        @endphp

                        <tr>
                            <td>{{ $record?->catalog_number ?? '-' }}</td>
                            <td>{{ $record?->grade?->name ?? '-' }}</td>
                            <td>{{ $record?->weaponBranch?->name ?? '-' }}</td>
                            <td>{{ $record?->name }}</td>
                            <td class="center">{{ $meal?->breakfast ? 'Sí' : 'No' }}</td>
                            <td class="center">{{ $meal?->lunch ? 'Sí' : 'No' }}</td>
                            <td class="center">{{ $meal?->dinner ? 'Sí' : 'No' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($i < 4)
                <pagebreak />
            @endif
        @endfor
    </body>

</html>
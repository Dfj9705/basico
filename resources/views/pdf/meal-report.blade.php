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

        <table>
            <thead>
                <tr>
                    <th>Catálogo</th>
                    <th>Grado</th>
                    <th>Arma</th>
                    <th>Nombre</th>
                    <th>Lunes</th>
                    <th>Martes</th>
                    <th>Miércoles</th>
                    <th>Jueves</th>
                    <th>Viernes</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($records as $record)
                    <tr>
                        <td>{{ $record?->catalog_number ?? '-' }}</td>
                        <td>{{ $record?->grade?->name ?? '-' }}</td>
                        <td>{{ $record?->weaponBranch?->name ?? '-' }}</td>
                        <td>{{ $record?->name }}</td>

                        @for ($i = 0; $i < 5; $i++)
                            @php
                                $date = $weekStart->copy()->addDays($i)->toDateString();

                                $meal = $record->mealAttendances->first(function ($meal) use ($date) {
                                    return \Carbon\Carbon::parse($meal->date)->toDateString() === $date;
                                });
                            @endphp

                            <td class="center small">
                                {{ $meal && ($meal->breakfast || $meal->lunch || $meal->dinner) ? 'Sí' : 'No' }}
                            </td>
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="center">
                            No hay usuarios para mostrar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>

</html>
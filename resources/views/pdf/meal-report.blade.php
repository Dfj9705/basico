<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <style>
            body {
                font-family: sans-serif;
                font-size: 11px;
            }

            h2 {
                text-align: center;
                margin-bottom: 5px;
            }

            .date {
                text-align: center;
                margin-bottom: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th {
                background: #f0f0f0;
            }

            th,
            td {
                border: 1px solid #444;
                padding: 6px;
            }

            .center {
                text-align: center;
            }
        </style>
    </head>

    <body>
        <h2>Reporte de Alimentación</h2>
        <div class="date">
            Fecha: {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
        </div>

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
                @forelse ($records as $record)
                    <tr>
                        <td>{{ $record?->catalog_number ?? '-' }}</td>
                        <td>{{ $record?->grade?->name ?? '-' }}</td>
                        <td>{{ $record?->weaponBranch?->name ?? '-' }}</td>
                        <td>{{ $record?->name }}</td>

                        @php
                            $meal = $record->mealAttendances->first();
                        @endphp

                        <td>{{ $meal?->breakfast ? 'Sí' : 'No' }}</td>
                        <td>{{ $meal?->lunch ? 'Sí' : 'No' }}</td>
                        <td>{{ $meal?->dinner ? 'Sí' : 'No' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="center">
                            No hay registros para esta fecha.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>

</html>
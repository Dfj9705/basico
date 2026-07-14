<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Reporte de Turnos</title>
        <style>
            body {
                font-family: 'Helvetica', 'Arial', sans-serif;
                color: #333;
            }

            .header {
                text-align: center;
                margin-bottom: 15px;
            }

            .header h1 {
                font-size: 20px;
                margin: 0;
                text-transform: uppercase;
                color: #1e3a8a;
            }

            .header h2 {
                font-size: 14px;
                margin: 5px 0 0 0;
                color: #4b5563;
            }

            .calendar-table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            }

            .calendar-table th {
                background-color: #1e3a8a;
                color: #ffffff;
                font-size: 11px;
                font-weight: bold;
                padding: 6px;
                text-align: center;
                border: 1px solid #1e3a8a;
            }

            .calendar-table td {
                border: 1px solid #d1d5db;
                height: 95px;
                /* Altura más amplia para albergar múltiples asignaciones */
                vertical-align: top;
                padding: 4px;
                font-size: 9px;
            }

            .day-number {
                font-weight: bold;
                color: #111827;
                margin-bottom: 4px;
                display: block;
                font-size: 11px;
            }

            .empty-day {
                background-color: #f3f4f6;
            }

            .shift-container {
                margin-top: 2px;
            }

            .user-shift-badge {
                padding: 2px 4px;
                color: #ffffff;
                border-radius: 2px;
                font-size: 8px;
                margin-bottom: 3px;
                line-height: 1.1;
            }

            .user-name {
                font-weight: bold;
            }

            .shift-name {
                font-style: italic;
                opacity: 0.9;
            }
        </style>
    </head>

    <body>

        <div class="header">
            <h1>Programación General de Turnos</h1>
            <h2>Mes: {{ ucfirst($monthName) }} {{ $year }}</h2>
        </div>

        <table class="calendar-table">
            <thead>
                <tr>
                    <th>Domingo</th>
                    <th>Lunes</th>
                    <th>Martes</th>
                    <th>Miércoles</th>
                    <th>Jueves</th>
                    <th>Viernes</th>
                    <th>Sábado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @php $currentCol = 0; @endphp

                    <!-- 1. Espacios en blanco para empezar en el día de semana correcto -->
                    @for($i = 0; $i < $startOfWeekDay; $i++)
                        <td class="empty-day"></td>
                        @php $currentCol++; @endphp
                    @endfor

                    <!-- 2. Días del mes y sus asignados -->
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @if($currentCol == 7)
                            </tr>
                            <tr>
                                @php $currentCol = 0; @endphp
                        @endif

                        <td>
                            <span class="day-number">{{ $day }}</span>

                            <div class="shift-container">
                                @if(count($matrix[$day]) > 0)
                                    @foreach($matrix[$day] as $userShift)
                                        <div class="user-shift-badge" style="background-color: {{ $userShift['color'] }};">
                                            <span class="user-name">{{ $userShift['userName'] }}</span>:
                                            <span class="shift-name">{{ $userShift['shiftName'] }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        @php $currentCol++; @endphp
                    @endfor

                    <!-- 3. Espacios en blanco al final de la última semana -->
                    @while($currentCol < 7)
                        <td class="empty-day"></td>
                        @php $currentCol++; @endphp
                    @endwhile
                </tr>
            </tbody>
        </table>

    </body>

</html>
let calendar;

document.addEventListener('DOMContentLoaded', function() {
    const eventos = citasData.map(cita => {
        let color = '#0d6efd'; 
        if (cita.estatus === 'atendida') color = '#198754';
        else if (cita.estatus === 'cancelada') color = '#dc3545';
        
        let horaCorta = cita.hora.substring(0, 5); 

        return {
            id: cita.cita_id,
            title: `${horaCorta} - ${cita.paciente_nombre} ${cita.paciente_apellido}`, 
            start: cita.fecha + 'T' + cita.hora,
            allDay: false,
            color: color,
            extendedProps: {
                paciente: `${cita.paciente_nombre} ${cita.paciente_apellido}`,
                paciente_cedula: cita.paciente_cedula,
                quiropedista: `${cita.quiropedista_nombre} ${cita.quiropedista_apellido}`,
                servicio: cita.servicio_nombre,
                estatus: cita.estatus,
                hora: cita.hora
            }
        };
    });

    var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                displayEventTime: false, 
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,dayGridDay'
        },
        buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día' },
        events: eventos,
        height: 'auto',
        eventDisplay: 'block',
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        eventDidMount: function(info) {
            info.el.setAttribute('title', 
                `${info.event.extendedProps.hora} - ${info.event.extendedProps.paciente}\nQuiropedista: ${info.event.extendedProps.quiropedista}\nServicio: ${info.event.extendedProps.servicio}\nEstatus: ${info.event.extendedProps.estatus}`
            );
            info.el.style.whiteSpace = 'normal';
            info.el.style.wordWrap = 'break-word';
            info.el.style.overflow = 'hidden';
            info.el.style.lineHeight = '1.2';
        },
        dateClick: function(info) {
            mostrarCitasDelDia(info.dateStr);
        },
        eventClick: function(info) {
            // Puedes dejar esto o abrir un modal más estilizado
        },
        dayCellDidMount: function(info) {
            const fecha = info.date.toISOString().split('T')[0];
            const citasDelDia = citasData.filter(c => c.fecha === fecha);
            if (citasDelDia.length > 0) {
                const indicadores = document.createElement('div');
                indicadores.className = 'd-flex justify-content-center gap-1 mt-1';
                
                const programadas = citasDelDia.filter(c => c.estatus === 'programada').length;
                const atendidas = citasDelDia.filter(c => c.estatus === 'atendida').length;
                const canceladas = citasDelDia.filter(c => c.estatus === 'cancelada').length;
                
                if (programadas > 0) { const dot = document.createElement('span'); dot.className = 'cita-indicador programada'; dot.setAttribute('title', `${programadas} programada(s)`); indicadores.appendChild(dot); }
                if (atendidas > 0) { const dot = document.createElement('span'); dot.className = 'cita-indicador atendida'; dot.setAttribute('title', `${atendidas} atendida(s)`); indicadores.appendChild(dot); }
                if (canceladas > 0) { const dot = document.createElement('span'); dot.className = 'cita-indicador cancelada'; dot.setAttribute('title', `${canceladas} cancelada(s)`); indicadores.appendChild(dot); }
                
                info.el.querySelector('.fc-daygrid-day-frame').appendChild(indicadores);
            }
        }
    });
    calendar.render();
});

function mostrarCitasDelDia(fecha) {
    const citasDelDia = citasData.filter(c => c.fecha === fecha);
    const fechaFormateada = new Date(fecha).toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    document.getElementById('fechaSeleccionada').textContent = fechaFormateada;
    
    const listaCitas = document.getElementById('listaCitasDia');
    if (citasDelDia.length === 0) {
        listaCitas.innerHTML = `<div class="text-center py-5"><i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i><h5 class="text-muted">No hay citas para este día</h5></div>`;
    } else {
        let html = '';
        citasDelDia.sort((a, b) => a.hora.localeCompare(b.hora));
        citasDelDia.forEach(cita => {
            html += `
                <div class="cita-item ${cita.estatus}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="hora-cita">${cita.hora}</span>
                            <strong>${cita.paciente_nombre} ${cita.paciente_apellido}</strong><br>
                            <small class="text-muted">C.I: ${cita.paciente_cedula}</small>
                        </div>
                        <span class="badge bg-${cita.estatus === 'programada' ? 'warning text-dark' : (cita.estatus === 'atendida' ? 'success' : 'danger')}">${cita.estatus}</span>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted"><i class="bi bi-person"></i> Quiropedista: ${cita.quiropedista_nombre} ${cita.quiropedista_apellido}</small><br>
                        <small class="text-muted"><i class="bi bi-scissors"></i> Servicio: ${cita.servicio_nombre}</small>
                    </div>
                </div>`;
        });
        listaCitas.innerHTML = html;
    }
    const modal = new bootstrap.Modal(document.getElementById('modalCitasDia'));
    modal.show();
}
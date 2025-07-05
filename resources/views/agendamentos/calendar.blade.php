<x-app-layout>
<div class="container">
    <h2 class="mb-4">📅 Calendário de Agendamentos</h2>

    <div id="calendar"></div>
    <!-- Modal -->
<div class="modal fade" id="detalheModal" tabindex="-1" aria-labelledby="detalheModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detalheModalLabel">Detalhes do Agendamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body" id="modalBodyContent">
        <!-- Conteúdo preenchido via JavaScript -->
      </div>
    </div>
  </div>
</div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
              initialView: 'timeGridWeek', // ou 'timeGridDay' para ver hora
    slotMinTime: "08:00:00",
    slotMaxTime: "19:00:00",
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: @json($eventos),
            eventDidMount: function(info) {
    info.el.addEventListener('dblclick', function() {
        window.location.href = '/agendamentos/' + info.event.id + '/edit';
    });
},
           eventClick: function(info) {
    info.jsEvent.preventDefault();

    let detalhes = `
        <p><strong>Tipo:</strong> ${info.event.extendedProps.tipo}</p>
        <p><strong>Cliente:</strong> ${info.event.title}</p>
        <p><strong>Status:</strong> ${info.event.extendedProps.status}</p>
        <p><strong>Início:</strong> ${new Date(info.event.start).toLocaleString('pt-BR')}</p>
                <p><strong>Endereço:</strong> ${info.event.extendedProps.endereco}</p>
        <p><strong>Telefone:</strong> ${info.event.extendedProps.telefone}</p>
        <a href="/agendamentos/${info.event.id}/edit" class="btn btn-sm btn-primary">Editar</a>
    `;

    document.getElementById('modalBodyContent').innerHTML = detalhes;
    new bootstrap.Modal(document.getElementById('detalheModal')).show();
}
,
    dateClick: function(info) {
    const date = info.date; // Objeto Date local
    // Formata para yyyy-mm-dd
    const data = date.toISOString().slice(0,10);
    // Formata hora e minuto no horário local (ajustando para 2 dígitos)
    const horario = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');

    if (confirm("Criar novo agendamento para " + data + " às " + horario + "?")) {
        window.location.href = `/agendamentos/create?data=${data}&horario=${horario}`;
    }
},


            
        });

        calendar.render();
    });
</script>
</x-app-layout>

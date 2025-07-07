<x-app-layout>
<div class="container" style="display: flex; gap: 20px;">

    <!-- Lado esquerdo: Calendário -->
    <div style="flex: 2;">
        <h2 class="mb-4">📅 Calendário de Agendamentos</h2>

        <div id="calendar"></div>

        <!-- Nova área para detalhes, inicialmente oculta -->
        <div id="detalhesAgendamento" style="display:none; margin-top: 15px; padding: 15px; border: 1px solid #ccc; border-radius: 8px; background-color: #f8f9fa;">
            <h5>Detalhes do Agendamento</h5>
            <div id="conteudoDetalhes"></div>
        </div>
    </div>

    <!-- Lado direito: Formulário de agendamento -->
    <div style="flex: 1; border: 1px solid #ccc; padding: 15px; border-radius: 8px; height: fit-content;">
        <h2 id="formTitle">Novo Agendamento</h2>

        <form method="POST" id="formAgendamento" action="{{ route('agendamentos.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="agendamento_id" id="agendamento_id">

            <div class="mb-3">
                <label>Tipo</label>
                <select name="tipo" class="form-select" required>
                    <option value="entrega">Entrega</option>
                    <option value="retirada">Retirada</option>
                    <option value="assistencia">Assistência</option>
                    <option value="orcamento">Orçamento</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Data</label>
                <input type="date" name="data" id="inputData" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Horário</label>
                <input type="time" name="horario" id="inputHorario" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Nome do Cliente</label>
                <input type="text" name="nome_cliente" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Endereço</label>
                <textarea name="endereco" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label>Telefone</label>
                <input type="text" class="form-control" name="telefone" id="telefone">
            </div>

            <div class="mb-3">
                <label>Itens</label>
                <textarea name="itens" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label>Observação</label>
                <textarea name="observacao" class="form-control"></textarea>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-success">Agendar</button>
        </form>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: @json($eventos),

            eventClick: function(info) {
                info.jsEvent.preventDefault();

                const detalhes = `
                    <p><strong>Tipo:</strong> ${info.event.extendedProps.tipo}</p>
                    <p><strong>Cliente:</strong> ${info.event.title}</p>
                    <p><strong>Status:</strong> ${info.event.extendedProps.status}</p>
                    <p><strong>Início:</strong> ${new Date(info.event.start).toLocaleString('pt-BR')}</p>
                    <p><strong>Endereço:</strong> ${info.event.extendedProps.endereco}</p>
                    <p><strong>Telefone:</strong> ${info.event.extendedProps.telefone}</p>
                `;

                const container = document.getElementById('conteudoDetalhes');
                container.innerHTML = detalhes;

                // Mostrar a div de detalhes
                document.getElementById('detalhesAgendamento').style.display = 'block';

                // Opcional: rolar suavemente para a área dos detalhes
                document.getElementById('detalhesAgendamento').scrollIntoView({ behavior: 'smooth' });
            },

            eventDidMount: function(info) {
                info.el.addEventListener('dblclick', function () {
                    const confirmar = confirm("Deseja carregar este agendamento no formulário para editar?");
                    if (!confirmar) return;

                    const agendamento = info.event.extendedProps;

                    document.getElementById('formAgendamento').action = `/agendamentos/${info.event.id}`;
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('agendamento_id').value = info.event.id;
                    document.getElementById('formTitle').innerText = 'Editar Agendamento';
                    document.getElementById('submitBtn').innerText = 'Atualizar';

                    document.getElementById('inputData').value = info.event.startStr.slice(0,10);
                    document.getElementById('inputHorario').value = new Date(info.event.start).toISOString().slice(11,16);
                    document.querySelector('[name="tipo"]').value = agendamento.tipo || '';
                    document.querySelector('[name="nome_cliente"]').value = info.event.title || '';
                    document.querySelector('[name="endereco"]').value = agendamento.endereco || '';
                    document.querySelector('[name="telefone"]').value = agendamento.telefone || '';
                    document.querySelector('[name="itens"]').value = agendamento.itens || '';
                    document.querySelector('[name="observacao"]').value = agendamento.observacao || '';
                });
            },

            dateClick: function(info) {
                const confirmar = confirm("Deseja criar um novo agendamento nesta data?");
                if (!confirmar) return;

                limparFormulario();

                const dataStr = info.date.toISOString().slice(0,10);
                const horarioStr = info.date.getHours().toString().padStart(2, '0') + ':' + info.date.getMinutes().toString().padStart(2, '0');

                document.getElementById('inputData').value = dataStr;
                document.getElementById('inputHorario').value = horarioStr;
                document.querySelector('input[name="nome_cliente"]').focus();
            }
        });

        calendar.render();

        // Confirmação ao atualizar
        document.getElementById('formAgendamento').addEventListener('submit', function (e) {
            const id = document.getElementById('agendamento_id').value;
            if (id) {
                const confirmar = confirm("Tem certeza que deseja atualizar este agendamento?");
                if (!confirmar) {
                    e.preventDefault();
                }
            }
        });

        // *** NOVO: preencher formulário se vier dados para isso do Controller ***
        const cliente = @json($cliente);
        const dataPreenchida = @json($dataPreenchida);
        const horarioPreenchido = @json($horarioPreenchido);

        if (cliente) {
            limparFormulario();

            document.querySelector('input[name="nome_cliente"]').value = cliente.nome || '';
            document.querySelector('textarea[name="endereco"]').value = cliente.endereco || '';
            document.querySelector('input[name="telefone"]').value = cliente.telefone || '';
        }

        if (dataPreenchida) {
            document.getElementById('inputData').value = dataPreenchida;
        }

        if (horarioPreenchido) {
            document.getElementById('inputHorario').value = horarioPreenchido;
        }
    });

    function limparFormulario() {
        document.getElementById('formAgendamento').reset();
        document.getElementById('formAgendamento').action = "{{ route('agendamentos.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('agendamento_id').value = '';
        document.getElementById('formTitle').innerText = 'Novo Agendamento';
        document.getElementById('submitBtn').innerText = 'Agendar';
    }
</script>
</x-app-layout>

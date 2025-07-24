<x-app-layout>
<div class="container" style="display: flex; gap: 20px;">

    <!-- Lado esquerdo: Calendário -->
    <div style="flex: 2;">
        <a href="{{ route('agendamentos.index') }}" class="btn btn-info btn-sm mb-3">
            📋 Visualizar Lista
        </a>

        <h2 class="mb-4">Calendário de Agendamentos</h2>
        <div id="calendar"></div>

        <div id="detalhesAgendamento" style="display:none; margin-top: 15px; padding: 15px; border: 1px solid #ccc; border-radius: 8px; background-color: #f8f9fa;">
            <h5>Detalhes do Agendamento</h5>
            <div id="conteudoDetalhes"></div>
        </div>
    </div>

    <!-- Formulário -->
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
                <input type="checkbox" id="clienteExistenteCheckbox">
                <label for="clienteExistenteCheckbox">Cliente Existente</label>
            </div>

            <div class="mb-3" id="selectClienteWrapper" style="display: none;">
                <label for="selectCliente">Selecione o Cliente</label>
                <select id="selectCliente" class="form-select">
                    <option value="">-- Escolha um cliente --</option>
                    @foreach($clientes as $c)
                        <option 
                            value="{{ $c->id }}"
                            data-nome="{{ $c->nome }}"
                            data-endereco="{{ $c->endereco }}"
                            data-telefone="{{ $c->telefone }}">
                            {{ $c->nome }} - {{ $c->endereco }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Nome do Cliente</label>
                <input type="text" name="nome_cliente" id="nome_cliente" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Endereço</label>
                <textarea name="endereco" id="endereco" class="form-control" required></textarea>
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

            <div class="d-flex gap-2" style="margin-top: 10px;">
                <button type="submit" id="submitBtn" class="btn btn-success">Agendar</button>
                <button 
                    type="button" 
                    id="deleteBtn" 
                    class="btn btn-danger" 
                    style="display: none;"
                    onclick="confirmDelete()"
                >
                    Excluir
                </button>
            </div>
        </form>
    </div>

</div>

{{-- Calendário e form JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        height: 'auto',
        slotMinTime: "09:00:00",
        slotMaxTime: "18:00:00",

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: @json($eventos),

        eventClick: function(info) {
            info.jsEvent.preventDefault();
            const ag = info.event.extendedProps;

            const detalhes = `
                <p><strong>Tipo:</strong> ${ag.tipo}</p>
                <p><strong>Cliente:</strong> ${info.event.title}</p>
                <p><strong>Status:</strong> ${ag.status}</p>
                <p><strong>Início:</strong> ${new Date(info.event.start).toLocaleString('pt-BR')}</p>
                <p><strong>Endereço:</strong> ${ag.endereco}</p>
                <p><strong>Telefone:</strong> ${ag.telefone}</p>
                <p><strong>Itens:</strong> ${ag.itens}</p>
                <p><strong>Observação:</strong> ${ag.observacao}</p>
            `;
            document.getElementById('conteudoDetalhes').innerHTML = detalhes;
            document.getElementById('detalhesAgendamento').style.display = 'block';
            document.getElementById('detalhesAgendamento').scrollIntoView({ behavior: 'smooth' });
        },

        eventDidMount: function(info) {
            info.el.addEventListener('dblclick', function () {
                if (!confirm("Deseja carregar este agendamento no formulário para editar?")) return;

                const ag = info.event.extendedProps;

                document.getElementById('formAgendamento').action = `/agendamentos/${info.event.id}`;
                document.getElementById('formMethod').value = 'PUT';
                document.getElementById('agendamento_id').value = info.event.id;
                document.getElementById('formTitle').innerText = 'Editar Agendamento';
                document.getElementById('submitBtn').innerText = 'Atualizar';

                document.getElementById('inputData').value = info.event.startStr.slice(0,10);
                document.getElementById('inputHorario').value = new Date(info.event.start).toISOString().slice(11,16);
                document.querySelector('[name="tipo"]').value = ag.tipo || '';
                document.getElementById('nome_cliente').value = info.event.title || '';
                document.getElementById('endereco').value = ag.endereco || '';
                document.getElementById('telefone').value = ag.telefone || '';
                document.querySelector('[name="itens"]').value = ag.itens || '';
                document.querySelector('[name="observacao"]').value = ag.observacao || '';

                toggleDeleteButton();
            });
        },

        dateClick: (function () {
            let lastClickTime = 0;
            let clickTimeout;

            return function(info) {
                const now = new Date().getTime();
                const diff = now - lastClickTime;
                if (clickTimeout) clearTimeout(clickTimeout);

                if (diff < 400) {
                    if (confirm("Deseja criar um novo agendamento nesta data?")) {
                        limparFormulario();
                        const dataStr = info.date.toISOString().slice(0, 10);
                        const horaStr = info.date.getHours().toString().padStart(2, '0') + ':' + info.date.getMinutes().toString().padStart(2, '0');
                        document.getElementById('inputData').value = dataStr;
                        document.getElementById('inputHorario').value = horaStr;
                        document.getElementById('nome_cliente').focus();
                    }
                } else {
                    clickTimeout = setTimeout(() => {
                        const dataStr = info.date.toISOString().slice(0, 10);
                        const horaStr = info.date.getHours().toString().padStart(2, '0') + ':' + info.date.getMinutes().toString().padStart(2, '0');
                        document.getElementById('inputData').value = dataStr;
                        document.getElementById('inputHorario').value = horaStr;
                    }, 300);
                }

                lastClickTime = now;
            };
        })()
    });

    calendar.render();

    // Confirmação ao atualizar
    document.getElementById('formAgendamento').addEventListener('submit', function (e) {
        const id = document.getElementById('agendamento_id').value;
        if (id && !confirm("Tem certeza que deseja atualizar este agendamento?")) {
            e.preventDefault();
        }
    });

    // Dados do controller
    const cliente = @json($cliente);
    const dataPreenchida = @json($dataPreenchida);
    const horarioPreenchido = @json($horarioPreenchido);
    const items = @json($items ?? '');
    const obs_retirada = @json($obs_retirada ?? '');

    if (cliente) {
        limparFormulario();
        document.getElementById('nome_cliente').value = cliente.nome || '';
        document.getElementById('endereco').value = cliente.endereco || '';
        document.getElementById('telefone').value = cliente.telefone || '';
    }

    if (dataPreenchida) document.getElementById('inputData').value = dataPreenchida;
    if (horarioPreenchido) document.getElementById('inputHorario').value = horarioPreenchido;

    // Preencher itens e observação (se tiver)
    document.querySelector('[name="itens"]').value = items || '';
    document.querySelector('[name="observacao"]').value = obs_retirada || '';

    toggleDeleteButton();
});

function limparFormulario() {
    document.getElementById('formAgendamento').reset();
    document.getElementById('formAgendamento').action = "{{ route('agendamentos.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('agendamento_id').value = '';
    document.getElementById('formTitle').innerText = 'Novo Agendamento';
    document.getElementById('submitBtn').innerText = 'Agendar';

    toggleDeleteButton();
}

function toggleDeleteButton() {
    const agendamentoId = document.getElementById('agendamento_id').value;
    const deleteBtn = document.getElementById('deleteBtn');
    if (agendamentoId) {
        deleteBtn.style.display = 'inline-block';
    } else {
        deleteBtn.style.display = 'none';
    }
}

function confirmDelete() {
    if (!confirm('Tem certeza que deseja excluir este agendamento?')) return;

    const agendamentoId = document.getElementById('agendamento_id').value;
    if (!agendamentoId) {
        alert('Nenhum agendamento selecionado para exclusão.');
        return;
    }

    // Criar form para exclusão dinamicamente e enviar
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/agendamentos/${agendamentoId}`;
    
    // CSRF token — pega da meta tag
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    form.innerHTML = `
        <input type="hidden" name="_token" value="${token}">
        <input type="hidden" name="_method" value="DELETE">
    `;
    
    document.body.appendChild(form);
    form.submit();
}
</script>

{{-- Cliente Existente Script --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('clienteExistenteCheckbox');
    const selectWrapper = document.getElementById('selectClienteWrapper');
    const selectCliente = document.getElementById('selectCliente');

    checkbox.addEventListener('change', () => {
        selectWrapper.style.display = checkbox.checked ? 'block' : 'none';
        if (!checkbox.checked) {
            selectCliente.value = '';
            document.querySelector('[name="nome_cliente"]').value = '';
            document.querySelector('[name="endereco"]').value = '';
            document.querySelector('[name="telefone"]').value = '';
            document.querySelector('[name="itens"]').value = '';
            document.querySelector('[name="observacao"]').value = '';
        }
    });

    selectCliente.addEventListener('change', () => {
        const option = selectCliente.options[selectCliente.selectedIndex];

        const nome = option.dataset.nome || '';
        const endereco = option.dataset.endereco || '';
        const telefone = option.dataset.telefone || '';
        const clienteId = option.value;

        document.getElementById('nome_cliente').value = nome;
        document.getElementById('endereco').value = endereco;
        document.getElementById('telefone').value = telefone;

        // Buscar itens e observação via AJAX
        if (clienteId) {
            fetch(`/clientes/${clienteId}/itens`)
                .then(response => response.json())
                .then(data => {
                    const campoItens = document.querySelector('[name="itens"]');
                    const campoObs = document.querySelector('[name="observacao"]');

                    campoItens.value = data.itens || '';
                    campoObs.value = data.observacao || '';

                    campoItens.dispatchEvent(new Event('input'));
                    campoObs.dispatchEvent(new Event('input'));
                })
                .catch(error => {
                    console.error('Erro ao buscar itens e observação do cliente:', error);
                });
        } else {
            document.querySelector('[name="itens"]').value = '';
            document.querySelector('[name="observacao"]').value = '';
        }
    });
});
</script>

</x-app-layout>

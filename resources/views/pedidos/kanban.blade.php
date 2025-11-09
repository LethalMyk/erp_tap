<x-app-layout>
    <div class="p-4 overflow-x-auto">
        <h1 class="text-2xl font-bold mb-4">Controle de Pedidos - Kanban</h1>
        <div class="flex space-x-4">

            @foreach($etapas as $etapa)
                @php
                    $corEtapa = match($etapa) {
                        'Orçamento' => 'bg-yellow-200',
                        'Agendar' => 'bg-blue-200',
                        'Retirar' => 'bg-indigo-200',
                        'Montado' => 'bg-green-200',
                        'Desmanchado' => 'bg-red-200',
                        default => 'bg-gray-100',
                    };
                @endphp

                <div class="rounded p-2 w-64 flex-shrink-0 {{ $corEtapa }}">
                    <h2 class="font-bold mb-2">{{ $etapa }}</h2>
                    <div class="space-y-2 dropzone p-1" data-status="{{ $etapa }}">
                        @foreach($pedidos->filter(fn($p) => $p->andamento === $etapa) as $pedido)
                            @php
                                $atrasado = $pedido->data_retirada && \Carbon\Carbon::parse($pedido->data_retirada)->isPast();
                            @endphp
                            <div class="bg-white p-2 rounded shadow cursor-move card {{ $atrasado ? 'border-2 border-red-500' : '' }}"
                                 draggable="true"
                                 data-pedido-id="{{ $pedido->id }}">
                                <strong>#{{ $pedido->id }} - {{ $pedido->cliente?->nome ?? 'Cliente não informado' }}</strong><br>

                                {{-- Itens --}}
                                @if($pedido->items->count())
                                    @foreach($pedido->items as $item)
                                        <div class="text-sm text-gray-600">{{ $item->nome_item }} ({{ $item->quant_item }}x)</div>
                                    @endforeach
                                @else
                                    <div class="text-sm text-gray-600">Sem itens</div>
                                @endif

                                {{-- Agendamento --}}
                                @if($pedido->agendamento)
                                    <div class="text-xs text-blue-500 mt-1">
                                        Agendado: {{ \Carbon\Carbon::parse($pedido->agendamento->data)->format('d/m/Y') }}
                                    </div>
                                @endif

                                {{-- Valor --}}
                                <div class="text-sm text-gray-800 mt-1 font-semibold">
                                    R$ {{ number_format($pedido->valor, 2, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>
    </div>

    <script>
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('dragstart', function(event) {
                event.dataTransfer.setData('text/plain', event.target.dataset.pedidoId);
            });
        });

        document.querySelectorAll('.dropzone').forEach(zone => {
            zone.addEventListener('dragover', function(event) {
                event.preventDefault();
            });

            zone.addEventListener('drop', function(event) {
                event.preventDefault();
                let pedidoId = event.dataTransfer.getData('text/plain');
                let novaStatus = this.dataset.status;
                let card = document.querySelector(`.card[data-pedido-id='${pedidoId}']`);

                if(card && this !== card.parentNode) {
                    this.appendChild(card);

                    fetch('{{ route("pedidos.updateStatus") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ pedido_id: parseInt(pedidoId), status: novaStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(!data.success) alert('Erro ao atualizar status!');
                    })
                    .catch(() => alert('Erro ao atualizar status!'));
                }
            });
        });
    </script>

    <style>
        .dropzone {
            min-height: 50px;
            border: 2px dashed #ccc;
            border-radius: 6px;
            padding: 4px;
        }
        .card:hover {
            background-color: #f0f0f0;
        }
        .border-red-500 {
            border-width: 2px;
        }
    </style>
</x-app-layout>

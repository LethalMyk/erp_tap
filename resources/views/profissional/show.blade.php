<x-app-layout>
    <h2>Detalhes do Profissional</h2>
    <p>Nome: {{ $profissional->nome }}</p>
    <p>Cargo: {{ $profissional->cargo }}</p>
    <a href="{{ route('profissional.index') }}">Voltar</a>
</x-app-layout>

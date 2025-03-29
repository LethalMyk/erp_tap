<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold mb-4">{{ __("You're logged in!") }}</h3>
                    <p class="text-lg mb-6">Aqui você pode gerenciar os pedidos e acessar outras funcionalidades.</p>
                </div>

                <!-- Botões de navegação com estilo -->
                <div class="flex space-x-6 justify-center p-6">
                    <!-- Botão Criar Pedido -->
                    <a href="{{ route('pedidos.create') }}" class="bg-blue-500 hover:bg-blue-700 text-blue font-bold py-3 px-6 rounded-lg shadow-md transform transition duration-300 ease-in-out hover:scale-105">
                        Criar Pedido
                    </a>

                
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight font-retro">
            {{ __('Połącz z ClickUp') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <h3 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200 font-retro">
                            {{ __('Połącz z ClickUp') }}
                        </h3>
                    </div>

                    <!-- Wiadomość o statusie -->
                    @if (session('status'))
                        <div class="mb-4">
                            <div class="text-sm font-medium text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-800 p-4 rounded-lg font-retro" role="alert">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif

                    <!-- Sprawdź, czy użytkownik ma już połączony ClickUp -->
                    @if (Auth::user()->cu_key)
                        <div class="mb-4">
                            <div class="text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-800 p-4 rounded-lg font-retro" role="alert">
                                {{ __('Masz już połączone konto ClickUp.') }}
                            </div>
                        </div>

                        <!-- Przycisk do odłączenia ClickUp -->
                        <form action="{{ route('clickup.disconnect') }}" method="POST" class="text-center">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-block px-4 py-2 bg-red-600 text-white text-sm font-retro rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-700 dark:hover:bg-red-600 dark:focus:ring-red-400 transition duration-200">
                                {{ __('Odłącz ClickUp') }}
                            </button>
                        </form>
                    @else
                        <!-- Przycisk do połączenia z ClickUp -->
                        <div class="text-center">
                            <a href="{{ route('clickup.authorize') }}" class="inline-block px-4 py-2 bg-blue-600 text-white text-sm font-retro rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-blue-400 transition duration-200">
                                {{ __('Połącz ClickUp') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

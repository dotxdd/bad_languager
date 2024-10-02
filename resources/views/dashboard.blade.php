<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight font-retro">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-200 dark:bg-gray-900 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 font-retro">
                    {{ __("Jestes zalogowany, witaj!") }}
                </div>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mt-6">
                @if(\Illuminate\Support\Facades\Auth::user()->tr_key)
                <!-- Kafelek do raportu miesięcznego Trello -->
                <a href="{{ route('trello.monthly.report') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 font-retro">{{ __('Raport Trello Miesięczny') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-2 font-retro">{{ __('Przejdź do raportu miesięcznego Trello.') }}</p>
                </a>

                <!-- Kafelek do raportu całościowego Trello -->
                <a href="{{ route('trello.whole.report') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 font-retro">{{ __('Raport Trello Całościowy') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-2 font-retro">{{ __('Przejdź do raportu całościowego Trello.') }}</p>
                </a>
                @endif
                    @if(\Illuminate\Support\Facades\Auth::user()->cu_key)

                    <!-- Kafelek do raportu całościowego ClickUp -->
                <a href="{{ route('clickup.whole.report') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 font-retro">{{ __('Raport ClickUp Całościowy') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-2 font-retro">{{ __('Przejdź do raportu całościowego ClickUp.') }}</p>
                </a>

                <!-- Kafelek do raportu miesięcznego ClickUp -->
                <a href="{{ route('clickup.monthly.report') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 font-retro">{{ __('Raport ClickUp Miesięczny') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-2 font-retro">{{ __('Przejdź do raportu miesięcznego ClickUp.') }}</p>
                </a>
                    @endif

            </div>
        </div>
    </div>
</x-app-layout>

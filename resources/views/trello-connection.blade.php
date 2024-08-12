<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Trello connect') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <h3 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">{{ __('Connect to Trello') }}</h3>
                    </div>

                    @if (session('status'))
                        <div class="mb-4">
                            <div class="alert alert-success text-sm font-medium text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-800 p-4 rounded-lg" role="alert">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif

                    @if (Auth::user()->tr_key)
                        <div class="mb-4">
                            <div class="alert alert-info text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-800 p-4 rounded-lg" role="alert">
                                {{ __('You have already connected your Trello account.') }}
                            </div>
                        </div>
                        <form action="{{ route('trello.disconnect') }}" method="POST" class="text-center">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger inline-block px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-700 dark:hover:bg-red-600 dark:focus:ring-red-400">
                                {{ __('Disconnect Trello') }}
                            </button>
                        </form>
                    @else
                        <div class="text-center">
                            <a href="{{ route('login.trello') }}" class="btn btn-primary inline-block px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-blue-400">
                                {{ __('Connect Trello') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

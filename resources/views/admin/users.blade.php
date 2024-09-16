<x-app-layout>
    <div class="container mx-auto py-8">
        <h1 class="text-4xl font-bold text-center text-blue-900 mb-8 font-retro">Lista Użytkowników</h1>

        <!-- Wyświetlanie wiadomości o sukcesie lub błędzie -->
        @if (session('success'))
            <div class="alert alert-success bg-blue-100 text-blue-700 border-l-4 border-blue-500 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger bg-red-100 text-red-700 border-l-4 border-red-500 p-4 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabela dla dużych ekranów i kafelki dla małych ekranów -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-blue-50 shadow-md rounded-lg overflow-hidden hidden sm:table">
                <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="py-3 px-6 text-left font-retro">ID</th>
                    <th class="py-3 px-6 text-left font-retro">Imię</th>
                    <th class="py-3 px-6 text-left font-retro">Email</th>
                    <th class="py-3 px-6 text-center font-retro">Akcje</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    <tr class="border-b border-blue-200 hover:bg-blue-100">
                        <td class="py-4 px-6 font-retro">{{ $user->id }}</td>
                        <td class="py-4 px-6 font-retro">{{ $user->name }}</td>
                        <td class="py-4 px-6 font-retro">{{ $user->email }}</td>
                        <td class="py-4 px-6 text-center">
                            @if (Auth::id() !== $user->id)
                                <form action="{{ route('admin.users.login', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-blue-500 text-white font-retro py-2 px-4 rounded hover:bg-blue-700 transition duration-200">
                                        Zaloguj się jako {{ $user->name }}
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-500 font-retro">To twoje konto</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Kafelki dla małych ekranów -->
        <div class="sm:hidden">
            @foreach ($users as $user)
                <div class="bg-blue-50 shadow-md rounded-lg p-4 mb-4 border border-blue-200">
                    <div class="font-retro text-blue-900">
                        <strong>ID:</strong> {{ $user->id }}
                    </div>
                    <div class="font-retro text-blue-900">
                        <strong>Imię:</strong> {{ $user->name }}
                    </div>
                    <div class="font-retro text-blue-900 mb-2">
                        <strong>Email:</strong> {{ $user->email }}
                    </div>
                    <div class="text-center">
                        @if (Auth::id() !== $user->id)
                            <form action="{{ route('admin.users.login', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-blue-500 text-white font-retro py-2 px-4 rounded hover:bg-blue-700 transition duration-200">
                                    Zaloguj się jako {{ $user->name }}
                                </button>
                            </form>
                        @else
                            <span class="text-gray-500 font-retro">To twoje konto</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>

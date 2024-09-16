<x-guest-layout>
    <style>
        /* General styling for the layout */
        body {
            font-family: 'MyRetroFont', sans-serif;
            background-color: #F0F8FF;
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            align-items: center;
        }

        h1 {
            font-family: 'MyRetroFont', cursive;
            color: #00008B;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        label {
            font-weight: bold;
            color: #00008B;
            font-family: 'MyRetroFont', cursive;
        }

        input {
            padding: 0.75rem;
            border: 2px solid #00008B;
            border-radius: 5px;
            width: 100%;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
        }

        input:focus {
            outline: none;
            border-color: #1E90FF;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #00008B;
            color: white;
            text-transform: uppercase;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            margin-left: 1rem;
            font-family: 'MyRetroFont', cursive;
        }

        .btn:hover {
            background-color: #1E90FF;
        }

        a {
            color: #00008B;
            text-decoration: underline;
            font-family: 'MyRetroFont', cursive;
        }

        a:hover {
            color: #1E90FF;
        }
    </style>

    <div class="container">
        <!-- Title -->
        <h1>bad_languager</h1>

        <!-- Login Form -->
        <div class="form-container">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Haslo')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Zapamietaj mnie') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                            {{ __('Zapomniales hasla?') }}
                        </a>
                    @endif

                    <button type="submit" class="btn">
                        {{ __('Zaloguj sie') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

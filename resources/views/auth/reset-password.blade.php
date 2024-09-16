<x-guest-layout>
    <style>
        /* Styling similar to the previous pages */
        body {
            font-family: 'Figtree', sans-serif;
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
            font-family: 'Press Start 2P', cursive;
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
        }

        .btn:hover {
            background-color: #1E90FF;
        }

        a {
            color: #00008B;
            text-decoration: underline;
        }

        a:hover {
            color: #1E90FF;
        }
    </style>

    <div class="container">
        <!-- Title -->
        <h1>bad_languager</h1>

        <!-- Password Reset Form -->
        <div class="form-container">
            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button class="btn">
                        {{ __('Reset Password') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

<section class="bg-gray-100 dark:bg-gray-900 p-6 rounded-lg shadow-lg">
    <header class="mb-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 font-retro">
            {{ __('Aktualizacja hasła') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 font-retro">
            {{ __('Upewnij się, że Twoje konto używa długiego, losowego hasła, aby zapewnić bezpieczeństwo.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
            <x-input-label for="update_password_current_password" :value="__('Aktualne hasło')" class="font-retro" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full font-retro" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm mt-4">
            <x-input-label for="update_password_password" :value="__('Nowe hasło')" class="font-retro" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full font-retro" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm mt-4">
            <x-input-label for="update_password_password_confirmation" :value="__('Potwierdź hasło')" class="font-retro" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full font-retro" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 mt-6">
            <x-primary-button class="font-retro">{{ __('Zapisz') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400 font-retro"
                >{{ __('Zapisano.') }}</p>
            @endif
        </div>
    </form>
</section>

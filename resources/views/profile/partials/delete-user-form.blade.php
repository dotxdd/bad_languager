<section class="space-y-6 bg-gray-100 dark:bg-gray-900 p-6 rounded-lg shadow-lg">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 font-retro">
            {{ __('Usuń konto') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 font-retro">
            {{ __('Po usunięciu konta wszystkie jego zasoby i dane zostaną trwale usunięte. Przed usunięciem konta, proszę pobrać wszelkie dane lub informacje, które chcesz zachować.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="font-retro"
    >{{ __('Usuń konto') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 font-retro">
                {{ __('Czy na pewno chcesz usunąć swoje konto?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 font-retro">
                {{ __('Po usunięciu konta wszystkie jego zasoby i dane zostaną trwale usunięte. Proszę wprowadzić swoje hasło, aby potwierdzić, że chcesz trwale usunąć swoje konto.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Hasło') }}" class="sr-only font-retro" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4 font-retro"
                    placeholder="{{ __('Hasło') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="font-retro">
                    {{ __('Anuluj') }}
                </x-secondary-button>

                <x-danger-button class="font-retro">
                    {{ __('Usuń konto') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>

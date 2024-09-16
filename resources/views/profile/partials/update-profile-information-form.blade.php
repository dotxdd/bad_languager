<section class="bg-blue-100 dark:bg-gray-900 p-6 rounded-lg shadow-lg">
    <header class="mb-6">
        <h2 class="text-lg font-medium text-blue-900 dark:text-gray-100 font-retro">
            {{ __('Informacje o profilu') }}
        </h2>

        <p class="mt-1 text-sm text-gray-800 dark:text-gray-400 font-retro">
            {{ __("Zaktualizuj informacje o swoim koncie oraz adres e-mail.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
            <x-input-label for="name" :value="__('Imię')" class="font-retro" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full font-retro" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
            <x-input-label for="open_ai_key" :value="__('Klucz OpenAI')" class="font-retro" />
            <x-text-input id="open_ai_key" class="block mt-1 w-full font-retro" type="text" name="open_ai_key" :value="old('open_ai_key', $user->open_ai_key)" />
            <x-input-error :messages="$errors->get('open_ai_key')" class="mt-2" />
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm mt-4">
            <input type="hidden" name="is_downloaded_trello_mail" value="0">
            <x-input-label for="is_downloaded_trello_mail" :value="__('Chcę otrzymywać mail o pobraniu danych z Trello')" class="font-retro" />
            <input type="checkbox" id="is_downloaded_trello_mail" name="is_downloaded_trello_mail" value="1"
                   {{ old('is_downloaded_trello_mail', $user->is_downloaded_trello_mail) ? 'checked' : '' }} class="mr-2" />
            <x-input-error :messages="$errors->get('is_downloaded_trello_mail')" class="mt-2" />
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm mt-4">
            <input type="hidden" name="is_downloaded_clickup_mail" value="0">
            <x-input-label for="is_downloaded_clickup_mail" :value="__('Chcę otrzymywać mail o pobraniu danych z ClickUp')" class="font-retro" />
            <input type="checkbox" id="is_downloaded_clickup_mail" name="is_downloaded_clickup_mail" value="1"
                   {{ old('is_downloaded_clickup_mail', $user->is_downloaded_clickup_mail) ? 'checked' : '' }} class="mr-2" />
            <x-input-error :messages="$errors->get('is_downloaded_clickup_mail')" class="mt-2" />
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm mt-4">
            <input type="hidden" name="is_reportable_mail" value="0">
            <x-input-label for="is_reportable_mail" :value="__('Chcę otrzymywać mail o wygenerowaniu raportów')" class="font-retro" />
            <input type="checkbox" id="is_reportable_mail" name="is_reportable_mail" value="1"
                   {{ old('is_reportable_mail', $user->is_reportable_mail) ? 'checked' : '' }} class="mr-2" />
            <x-input-error :messages="$errors->get('is_reportable_mail')" class="mt-2" />
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm mt-4">
            <x-input-label for="email" :value="__('Email')" class="font-retro" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full font-retro" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4">
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-retro">
                        {{ __('Twój adres e-mail nie został zweryfikowany.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 font-retro">
                            {{ __('Kliknij tutaj, aby wysłać ponownie e-mail weryfikacyjny.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400 font-retro">
                            {{ __('Nowy link weryfikacyjny został wysłany na Twój adres e-mail.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 mt-6">
            <x-primary-button class="font-retro">{{ __('Zapisz') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
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

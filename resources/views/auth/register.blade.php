<x-guest-layout>
    <h2 class="text-2xl font-bold text-slate-900 mb-1">Create your account</h2>
    <p class="text-sm text-slate-500 mb-8">Sign up to start booking and tracking parcels</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-5">
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <!-- Email Address -->
        <div class="mb-5">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Phone -->
        <div class="mb-5">
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input id="phone" type="text" name="phone" :value="old('phone')" required autocomplete="tel" placeholder="e.g. 01711-100001" />
            <x-input-error :messages="$errors->get('phone')" />
        </div>

        <!-- Password -->
        <div class="mb-5">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-5">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-100 mt-6">
            <a class="text-sm text-slate-500 hover:text-indigo-600 transition-colors" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button>
                {{ __('Create Account') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

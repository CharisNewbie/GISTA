<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
    public function quickLogin(string $email): void
    {
        $this->form->email = $email;
        $this->form->password = 'password';
        $this->login();
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2 class="text-2xl font-bold text-gray-900 mb-1">BCA Logistic Assistant</h2>
    <p class="text-xs text-gray-500 mb-4">GISTA Payment &mdash; Pembayaran Eksternal V.1.0</p>

    <!-- Quick Demo Logins -->
    <div class="mb-5 p-3 bg-gista-50 border border-gista-200 rounded-xl space-y-2">
        <p class="text-[10px] font-bold text-gista-800 uppercase tracking-wider">Quick Login Demo (Password: password)</p>
        <div class="grid grid-cols-2 gap-1.5">
            <button type="button" wire:click="quickLogin('operator@bca.co.id')" class="px-2 py-1 text-[11px] bg-white border border-gista-200 hover:bg-gista-100 rounded text-gista-900 font-semibold text-left truncate">
                1. Operator (Input)
            </button>
            <button type="button" wire:click="quickLogin('checker@bca.co.id')" class="px-2 py-1 text-[11px] bg-white border border-gista-200 hover:bg-gista-100 rounded text-gista-900 font-semibold text-left truncate">
                2. Checker (Review)
            </button>
            <button type="button" wire:click="quickLogin('spv1@bca.co.id')" class="px-2 py-1 text-[11px] bg-white border border-gista-200 hover:bg-gista-100 rounded text-gista-900 font-semibold text-left truncate">
                3. SPV 1 (Unit Kerja)
            </button>
            <button type="button" wire:click="quickLogin('spv2@bca.co.id')" class="px-2 py-1 text-[11px] bg-white border border-gista-200 hover:bg-gista-100 rounded text-gista-900 font-semibold text-left truncate">
                4. SPV 2 (Biro Payment)
            </button>
        </div>
    </div>

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-gista-600 shadow-sm focus:ring-gista-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-gista-600 hover:text-gista-800 font-medium" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Sign In') }}
            </x-primary-button>
        </div>

        <p class="mt-6 text-center text-sm text-gray-500">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-gista-600 hover:text-gista-800 font-semibold" wire:navigate>Sign up</a>
        </p>
    </form>
</div>

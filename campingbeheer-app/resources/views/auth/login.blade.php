@extends('layouts.app')

@section('title', 'Login Dashboard')

@section('content')
    <section>
        <div class="max-w-md mx-auto border border-gray-300 rounded-lg p-6 bg-white shadow-sm">
            {{-- Hier komt de Login --}}
            <h1 class="text-center text-6xl mb-6">login</h1>

            <form id="login-form" method="POST" action="{{ route('login') }}" class="flex flex-col items-center space-y-4">
                @csrf

                @if(session('error'))
                    <div class="w-full text-red-600 mb-2">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="w-full text-green-600 mb-2">{{ session('success') }}</div>
                @endif
                <div class="w-full">
                    <label for="naam" class="block text-sm font-medium mb-1">Naam</label>
                    <input type="text" id="naam" name="naam" class="w-full border border-gray-300 rounded px-3 py-2" />
                </div>

                <div class="w-full">
                    <label for="password" class="block text-sm font-medium mb-1">Wachtwoord</label>
                    <input type="password" id="password" name="password"
                        class="w-full border border-gray-300 rounded px-3 py-2" />
                </div>

                <div>
                    <button id="btn-login"
                        class="text-2xl bg-accent text-white px-6 py-2 rounded hover:bg-accent-hover">Inloggen</button>
                </div>

                <div id="login-message" class="mt-4 text-sm text-green-600 hidden"></div>
            </form>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        (function () {
            const btn = document.getElementById('btn-login');
            const msg = document.getElementById('login-message');

            if (!btn) return;

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const naam = document.getElementById('naam').value.trim();
                const ww = document.getElementById('password').value.trim();

                msg.classList.remove('text-red-600', 'text-green-600', 'hidden');

                if (!naam || !ww) {
                    msg.textContent = 'Vul zowel naam als wachtwoord in.';
                    msg.classList.add('text-red-600');
                    return;
                }

                // Submit the form to let the server authenticate
                msg.textContent = 'Inloggen...';
                msg.classList.add('text-green-600');
                document.getElementById('login-form').submit();
            });
        })();
    </script>
@endsection
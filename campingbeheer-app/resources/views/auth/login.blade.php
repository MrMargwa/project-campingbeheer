@extends('layouts.app')

@section('title', 'Login Dashboard')

@section('content')
    <section class="flex min-h-[calc(100vh-14rem)] items-start justify-center pt-16">
        <div class="w-full max-w-xl">
            <div class="rounded-xl border border-border bg-surface p-10 shadow-sm">
                <h2 class="mb-15 text-center text-xl font-semibold text-primary sm:text-4xl">Inloggen</h2>

                <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    @if(session('error'))
                        <div class="rounded-lg bg-danger/10 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="rounded-lg bg-success/10 px-4 py-3 text-sm text-success">{{ session('success') }}</div>
                    @endif

                    <div>
                        <label for="naam" class="mb-1 block text-xs font-medium text-primary">Naam</label>
                        <input type="text" id="naam" name="naam"
                            class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary" />
                    </div>

                    <div>
                        <label for="password" class="mb-1 block text-xs font-medium text-primary">Wachtwoord</label>
                        <input type="password" id="password" name="password"
                            class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary" />
                    </div>

                    <div class="pt-2">
                        <button id="btn-login"
                            class="w-full rounded-lg bg-accent px-5 py-2.5 text-sm font-medium text-white transition hover:bg-accent-hover">Inloggen</button>
                    </div>

                    <div id="login-message" class="hidden text-center text-sm text-success"></div>
                </form>
            </div>
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

                msg.classList.remove('text-danger', 'text-success', 'hidden');

                if (!naam || !ww) {
                    msg.textContent = 'Vul zowel naam als wachtwoord in.';
                    msg.classList.add('text-danger');
                    return;
                }

                msg.textContent = 'Inloggen...';
                msg.classList.add('text-success');
                document.getElementById('login-form').submit();
            });
        })();
    </script>
@endsection
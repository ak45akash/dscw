<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — {{ config('dscw.business.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full items-center justify-center bg-gradient-to-br from-brand-800 via-brand-700 to-graphite-900 px-4">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center text-white">
            <p class="text-sm uppercase tracking-[0.2em] text-accent-400">Admin Panel</p>
            <h1 class="mt-2 text-3xl font-bold">{{ config('dscw.business.name') }}</h1>
        </div>

        <x-card>
            <h2 class="text-xl font-semibold text-graphite-900 dark:text-white">Sign in</h2>
            <p class="mt-1 text-sm text-graphite-500">Use your admin credentials to continue.</p>

            @if($errors->any())
                <x-alert type="error" class="mt-4">
                    {{ $errors->first() }}
                </x-alert>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <x-label for="email" required>Email</x-label>
                    <x-input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus />
                </div>

                <div>
                    <x-label for="password" required>Password</x-label>
                    <x-input type="password" name="password" id="password" required />
                </div>

                <label class="flex items-center gap-2 text-sm text-graphite-600 dark:text-graphite-300">
                    <input type="checkbox" name="remember" class="rounded border-graphite-300" @checked(old('remember'))>
                    Remember me
                </label>

                <x-button type="submit" class="w-full">Sign In</x-button>
            </form>
        </x-card>
    </div>
</body>
</html>

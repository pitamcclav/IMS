<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - ERA IMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    IMS PORTAL
                </h2>
            </div>
            <div class="mt-8 bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                @if (session('error'))
                    <div class="rounded-md bg-red-50 p-4 mb-4">
                        <div class="text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    </div>
                @elseif (session('success'))
                    <div class="rounded-md bg-green-50 p-4 mb-4">
                        <div class="text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                <form class="space-y-6" method="POST" action="{{ route('login.authenticate') }}">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            {{ __('Email Address') }}
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" required autofocus
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                value="{{ old('email') }}"
                            >
                            @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            {{ __('Password') }}
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                            >
                            @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-900">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            {{ __('Login') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

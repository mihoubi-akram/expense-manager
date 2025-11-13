<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Expense Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-2xl font-bold text-gray-900 text-center mb-6">Expense Manager</h1>
            <h2 class="text-lg text-gray-600 text-center mb-8">Create your account</h2>

            <div id="error-message" class="hidden bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6">
                <ul id="error-list" class="list-disc list-inside text-sm"></ul>
            </div>

            <form id="register-form">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        required
                        autofocus
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        placeholder="John Doe"
                    >
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        placeholder="your@email.com"
                    >
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        placeholder="••••••••"
                    >
                    <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        placeholder="••••••••"
                    >
                </div>

                <button
                    type="submit"
                    id="register-button"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                        Sign in
                    </a>
                </p>
            </div>
        </div>

        <p class="text-center text-gray-500 text-xs mt-6">
            &copy; {{ date('Y') }} Expense Manager. All rights reserved.
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Redirect if already authenticated
            window.auth.redirectIfAuthenticated();

        const form = document.getElementById('register-form');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const submitButton = document.getElementById('register-button');
        const errorMessage = document.getElementById('error-message');
        const errorList = document.getElementById('error-list');

        function showErrors(errors) {
            errorList.innerHTML = '';
            errorMessage.classList.remove('hidden');

            if (typeof errors === 'string') {
                const li = document.createElement('li');
                li.textContent = errors;
                errorList.appendChild(li);
            } else if (errors.errors) {
                Object.values(errors.errors).flat().forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    errorList.appendChild(li);
                });
            } else if (errors.message) {
                const li = document.createElement('li');
                li.textContent = errors.message;
                errorList.appendChild(li);
            }
        }

        function hideErrors() {
            errorMessage.classList.add('hidden');
            errorList.innerHTML = '';
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideErrors();

            const name = nameInput.value.trim();
            const email = emailInput.value.trim();
            const password = passwordInput.value;
            const passwordConfirmation = passwordConfirmationInput.value;

            if (!name || !email || !password || !passwordConfirmation) {
                showErrors('Please fill in all fields');
                return;
            }

            if (password !== passwordConfirmation) {
                showErrors('Passwords do not match');
                return;
            }

            if (password.length < 8) {
                showErrors('Password must be at least 8 characters');
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Creating account...';

            try {
                await window.auth.register(name, email, password, passwordConfirmation);
                window.location.href = '/dashboard';
            } catch (error) {
                console.error('Registration error:', error);
                showErrors(error);
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Create Account';
            }
            });
        });
    </script>
</body>
</html>

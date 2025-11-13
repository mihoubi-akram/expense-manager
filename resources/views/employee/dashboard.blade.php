<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Expense Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-gray-900">Expense Manager</h1>
                </div>
                <div class="flex items-center gap-4">
                    <span id="user-email" class="text-sm text-gray-600"></span>
                    <button
                        id="logout-button"
                        class="text-sm text-red-600 hover:text-red-700 font-medium"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div id="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-gray-600">Loading...</p>
        </div>

        <div id="dashboard-content" class="hidden">
            <div class="bg-white rounded-lg shadow-md p-8">
                <div class="text-center">
                    <div class="mb-4">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 text-blue-600 mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>

                    <h2 class="text-3xl font-bold text-gray-900 mb-2">
                        Welcome, <span id="user-name"></span>!
                    </h2>

                    <div class="inline-block mt-4 px-4 py-2 bg-blue-50 rounded-full">
                        <p class="text-sm text-blue-700 font-medium">
                            Role: <span id="user-role" class="capitalize"></span>
                        </p>
                    </div>

                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <p class="text-gray-600">
                            You are logged in as an employee.
                        </p>
                        <p class="text-sm text-gray-500 mt-2">
                            User ID: <span id="user-id"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div id="error-message" class="hidden bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
            <p class="text-sm"></p>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Check authentication on page load
            window.auth.checkAuthOnLoad();

        const loadingDiv = document.getElementById('loading');
        const dashboardContent = document.getElementById('dashboard-content');
        const errorMessage = document.getElementById('error-message');
        const logoutButton = document.getElementById('logout-button');

            // Load user data
            async function loadUserData() {
                try {
                    const response = await window.auth.getCurrentUser();
                    const user = response.user;

                // Update UI with user data
                document.getElementById('user-name').textContent = user.name;
                document.getElementById('user-email').textContent = user.email;
                document.getElementById('user-role').textContent = user.role.toLowerCase();
                document.getElementById('user-id').textContent = user.id;

                // Show dashboard content
                loadingDiv.classList.add('hidden');
                dashboardContent.classList.remove('hidden');
            } catch (error) {
                console.error('Failed to load user data:', error);
                loadingDiv.classList.add('hidden');
                errorMessage.classList.remove('hidden');
                errorMessage.querySelector('p').textContent = 'Failed to load user data. Please try again.';

                // If unauthorized, redirect to login
                    setTimeout(() => {
                        window.auth.removeToken();
                        window.location.href = '/login';
                    }, 2000);
                }
            }

            // Logout handler
            logoutButton.addEventListener('click', async () => {
                try {
                    await window.auth.logout();
                    window.location.href = '/login';
                } catch (error) {
                    console.error('Logout error:', error);
                    // Even if API call fails, remove token and redirect
                    window.auth.removeToken();
                    window.location.href = '/login';
                }
            });

            // Load user data on page load
            loadUserData();
        });
    </script>
</body>
</html>

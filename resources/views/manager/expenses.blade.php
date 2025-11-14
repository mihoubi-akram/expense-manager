<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manager - Expenses</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-6">
                    <h1 class="text-xl font-semibold text-gray-900">Expense Manager</h1>
                    <a href="/manager/expenses" class="text-sm text-blue-600 font-medium">Manager Dashboard</a>
                </div>
                <div class="flex items-center gap-4">
                    <span id="user-email" class="text-sm text-gray-600"></span>
                    <button id="logout-button" class="text-sm text-red-600 hover:text-red-700 font-medium">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Error/Success Messages -->
        <div id="message-bar" class="hidden mb-6 px-4 py-3 rounded-lg"></div>

        <!-- Stats Section -->
        <div id="stats-container" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="text-sm text-gray-600 mb-1">Total</div>
                <div id="stat-total" class="text-2xl font-bold text-gray-900">-</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="text-sm text-gray-600 mb-1">Approved</div>
                <div id="stat-approved" class="text-2xl font-bold text-green-600">-</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="text-sm text-gray-600 mb-1">Paid</div>
                <div id="stat-paid" class="text-2xl font-bold text-blue-600">-</div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="filter-status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Submitted</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="filter-category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="meal">Meal</option>
                        <option value="travel">Travel</option>
                        <option value="hotel">Hotel</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" id="filter-from-date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" id="filter-to-date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <button id="apply-filters-btn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                    Apply Filters
                </button>
                <button id="clear-filters-btn" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium">
                    Clear
                </button>
                <button id="export-csv-btn" class="ml-auto px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                    Export to CSV
                </button>
            </div>
        </div>

        <!-- Expenses List Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">All Expenses</h2>
            </div>

            <div id="loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-gray-600">Loading expenses...</p>
            </div>

            <div id="expenses-container" class="hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="expenses-tbody" class="bg-white divide-y divide-gray-200">
                            <!-- Expenses will be inserted here -->
                        </tbody>
                    </table>
                </div>

                <div id="pagination" class="mt-4 flex justify-center gap-2">
                    <!-- Pagination will be inserted here -->
                </div>
            </div>

            <div id="no-expenses" class="hidden text-center py-8 text-gray-500">
                No expenses found.
            </div>
        </div>
    </main>

    <!-- Reject Modal -->
    <div id="reject-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full" style="z-index: 9999;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div id="reject-modal-content" class="relative bg-white rounded-lg shadow-xl p-6 w-full max-w-md" style="z-index: 10000;">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Reject Expense</h3>
                    <p class="text-sm text-gray-600 mt-1">Please provide a reason for rejection (10-1000 characters)</p>
                </div>
                <textarea id="reject-comment" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter rejection reason..."></textarea>
                <div class="flex gap-2 mt-4">
                    <button id="confirm-reject-btn" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-gray-700 rounded-lg font-medium">
                        Reject
                    </button>
                    <button id="cancel-reject-btn" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.auth.checkAuthOnLoad();

            // State
            let currentFilters = {};
            let currentPage = 1;
            let rejectingExpenseId = null;

            // Elements
            const loading = document.getElementById('loading');
            const expensesContainer = document.getElementById('expenses-container');
            const noExpenses = document.getElementById('no-expenses');
            const expensesTbody = document.getElementById('expenses-tbody');
            const pagination = document.getElementById('pagination');
            const messageBar = document.getElementById('message-bar');
            const rejectModal = document.getElementById('reject-modal');
            const rejectComment = document.getElementById('reject-comment');

            // Logout
            document.getElementById('logout-button').addEventListener('click', async () => {
                try {
                    await window.auth.logout();
                    window.location.href = '/login';
                } catch (error) {
                    window.auth.removeToken();
                    window.location.href = '/login';
                }
            });

            // Load user email
            async function loadUserEmail() {
                try {
                    const response = await window.auth.getCurrentUser();
                    document.getElementById('user-email').textContent = response.user.email;
                } catch (error) {
                    console.error('Failed to load user:', error);
                }
            }

            // Show message
            function showMessage(message, type = 'success') {
                messageBar.className = `mb-6 px-4 py-3 rounded-lg ${type === 'error' ? 'bg-red-50 border border-red-200 text-red-600' : 'bg-green-50 border border-green-200 text-green-600'}`;
                messageBar.textContent = message;
                messageBar.classList.remove('hidden');
                setTimeout(() => messageBar.classList.add('hidden'), 5000);
            }

            // Load stats
            async function loadStats() {
                try {
                    const token = window.auth.getToken();
                    const response = await fetch('/api/stats/summary', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to load stats');

                    const response_data = await response.json();
                    const stats = response_data.data; // API wraps in data property

                    document.getElementById('stat-total').textContent = stats.expenses_count || 0;
                    document.getElementById('stat-approved').textContent = stats.by_status?.approved?.count || 0;
                    document.getElementById('stat-paid').textContent = stats.by_status?.paid?.count || 0;
                } catch (error) {
                    console.error('Failed to load stats:', error);
                }
            }

            // Load expenses
            async function loadExpenses() {
                try {
                    loading.classList.remove('hidden');
                    expensesContainer.classList.add('hidden');
                    noExpenses.classList.add('hidden');

                    const filters = { ...currentFilters, page: currentPage };
                    const data = await window.expenses.getExpenses(filters);

                    if (data.data.length === 0) {
                        noExpenses.classList.remove('hidden');
                    } else {
                        renderExpenses(data.data);
                        renderPagination(data.meta);
                        expensesContainer.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Failed to load expenses:', error);
                    showMessage('Failed to load expenses', 'error');
                } finally {
                    loading.classList.add('hidden');
                }
            }

            // Render expenses
            function renderExpenses(expenses) {
                expensesTbody.innerHTML = expenses.map((expense) => {
                    const actions = getActions(expense);
                    return `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${expense.title}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${expense.user?.name || expense.user?.email || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${window.expenses.formatCurrency(expense.amount, expense.currency)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">${expense.category}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${window.expenses.formatDate(expense.spent_at)}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${window.expenses.getStatusBadgeClass(expense.status)} capitalize">
                                    ${expense.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    ${actions}
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            // Get actions based on status
            function getActions(expense) {
                const actions = [];

                if (expense.status === 'submitted') {
                    actions.push(`<button onclick="approveExpense(${expense.id})" class="text-green-600 hover:text-green-900">Approve</button>`);
                    actions.push(`<button onclick="openRejectModal(${expense.id})" class="text-red-600 hover:text-red-900">Reject</button>`);
                } else if (expense.status === 'approved') {
                    actions.push(`<button onclick="payExpense(${expense.id})" class="text-blue-600 hover:text-blue-900">Pay</button>`);
                }

                return actions.length > 0 ? actions.join('') : '<span class="text-gray-400">No actions</span>';
            }

            // Render pagination
            function renderPagination(meta) {
                if (!meta || meta.last_page <= 1) {
                    pagination.innerHTML = '';
                    return;
                }

                const pages = [];
                for (let i = 1; i <= meta.last_page; i++) {
                    pages.push(`
                        <button onclick="goToPage(${i})" class="px-3 py-1 rounded ${i === meta.current_page ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}">
                            ${i}
                        </button>
                    `);
                }
                pagination.innerHTML = pages.join('');
            }

            // Global functions for inline handlers
            window.goToPage = (page) => {
                currentPage = page;
                loadExpenses();
            };

            window.approveExpense = async (id) => {
                if (!confirm('Are you sure you want to approve this expense?')) return;

                try {
                    const token = window.auth.getToken();
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch(`/api/expenses/${id}/approve`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (!response.ok) throw new Error('Failed to approve expense');

                    showMessage('Expense approved successfully');
                    loadExpenses();
                    loadStats();
                } catch (error) {
                    console.error('Failed to approve expense:', error);
                    showMessage('Failed to approve expense', 'error');
                }
            };

            window.openRejectModal = (id) => {
                rejectingExpenseId = id;
                rejectComment.value = '';
                rejectModal.classList.remove('hidden');
            };

            window.payExpense = async (id) => {
                if (!confirm('Are you sure you want to mark this expense as paid?')) return;

                try {
                    const token = window.auth.getToken();
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch(`/api/expenses/${id}/pay`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (!response.ok) throw new Error('Failed to mark expense as paid');

                    showMessage('Expense marked as paid successfully');
                    loadExpenses();
                    loadStats();
                } catch (error) {
                    console.error('Failed to mark expense as paid:', error);
                    showMessage('Failed to mark expense as paid', 'error');
                }
            };

            // Reject modal handlers
            document.getElementById('confirm-reject-btn').addEventListener('click', async () => {
                const comment = rejectComment.value.trim();

                if (comment.length < 10 || comment.length > 1000) {
                    showMessage('Comment must be between 10 and 1000 characters', 'error');
                    return;
                }

                try {
                    const token = window.auth.getToken();
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch(`/api/expenses/${rejectingExpenseId}/reject`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ comment })
                    });

                    if (!response.ok) throw new Error('Failed to reject expense');

                    showMessage('Expense rejected successfully');
                    rejectModal.classList.add('hidden');
                    rejectingExpenseId = null;
                    loadExpenses();
                    loadStats();
                } catch (error) {
                    console.error('Failed to reject expense:', error);
                    showMessage('Failed to reject expense', 'error');
                }
            });

            document.getElementById('cancel-reject-btn').addEventListener('click', () => {
                rejectModal.classList.add('hidden');
                rejectingExpenseId = null;
            });

            // Close modal when clicking outside
            document.getElementById('reject-modal').addEventListener('click', (e) => {
                if (e.target.id === 'reject-modal') {
                    rejectModal.classList.add('hidden');
                    rejectingExpenseId = null;
                }
            });

            // Export CSV
            document.getElementById('export-csv-btn').addEventListener('click', async () => {
                try {
                    const token = window.auth.getToken();
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const filters = { ...currentFilters };

                    // Create export
                    const response = await fetch('/api/exports/expenses', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(filters)
                    });

                    if (!response.ok) throw new Error('Failed to create export');

                    const exportData = await response.json();
                    const exportId = exportData.data.id;

                    showMessage('Export started. Preparing download...');

                    // Poll for export completion
                    let attempts = 0;
                    const maxAttempts = 30; // 30 seconds max
                    const pollInterval = setInterval(async () => {
                        attempts++;

                        try {
                            const statusResponse = await fetch(`/api/exports/${exportId}`, {
                                headers: {
                                    'Authorization': `Bearer ${token}`,
                                    'Accept': 'application/json'
                                }
                            });

                            if (!statusResponse.ok) throw new Error('Failed to check export status');

                            const statusData = await statusResponse.json();

                            if (statusData.data.status === 'completed') {
                                clearInterval(pollInterval);

                                // Download the file
                                const downloadUrl = `/api/exports/${exportId}/download`;
                                const downloadLink = document.createElement('a');
                                downloadLink.href = downloadUrl;
                                downloadLink.download = statusData.data.file_path?.split('/').pop() || 'expenses.csv';

                                // Add authorization header by using fetch and creating blob
                                const fileResponse = await fetch(downloadUrl, {
                                    headers: {
                                        'Authorization': `Bearer ${token}`
                                    }
                                });

                                const blob = await fileResponse.blob();
                                const url = window.URL.createObjectURL(blob);
                                downloadLink.href = url;
                                downloadLink.click();
                                window.URL.revokeObjectURL(url);

                                showMessage('Export completed and downloaded successfully');
                            } else if (statusData.data.status === 'failed') {
                                clearInterval(pollInterval);
                                showMessage('Export failed', 'error');
                            } else if (attempts >= maxAttempts) {
                                clearInterval(pollInterval);
                                showMessage('Export is taking too long. Please try again later.', 'error');
                            }
                        } catch (error) {
                            clearInterval(pollInterval);
                            console.error('Failed to check export status:', error);
                            showMessage('Failed to check export status', 'error');
                        }
                    }, 1000);

                } catch (error) {
                    console.error('Failed to export expenses:', error);
                    showMessage('Failed to export expenses', 'error');
                }
            });

            // Filters
            document.getElementById('apply-filters-btn').addEventListener('click', () => {
                currentFilters = {
                    status: document.getElementById('filter-status').value,
                    category: document.getElementById('filter-category').value,
                    from_date: document.getElementById('filter-from-date').value,
                    to_date: document.getElementById('filter-to-date').value,
                };
                currentPage = 1;
                loadExpenses();
            });

            document.getElementById('clear-filters-btn').addEventListener('click', () => {
                document.getElementById('filter-status').value = '';
                document.getElementById('filter-category').value = '';
                document.getElementById('filter-from-date').value = '';
                document.getElementById('filter-to-date').value = '';
                currentFilters = {};
                currentPage = 1;
                loadExpenses();
            });

            // Initial load
            loadUserEmail();
            loadStats();
            loadExpenses();
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Expenses - Expense Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-6">
                    <h1 class="text-xl font-semibold text-gray-900">Expense Manager</h1>
                    <a href="/dashboard" class="text-sm text-gray-600 hover:text-gray-900">Dashboard</a>
                    <a href="/expenses" class="text-sm text-blue-600 font-medium">My Expenses</a>
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
            </div>
        </div>

        <!-- Form Section (Hidden by default) -->
        <div id="expense-form-section" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 id="form-title" class="text-lg font-semibold text-gray-900">New Expense</h2>
                <button id="cancel-form-btn" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="expense-form">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input type="text" id="title" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                        <input type="number" id="amount" name="amount" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                        <select id="currency" name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="EUR">EUR</option>
                            <option value="USD">USD</option>
                            <option value="GBP">GBP</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select id="category" name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select category</option>
                            <option value="meal">Meal</option>
                            <option value="travel">Travel</option>
                            <option value="hotel">Hotel</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                        <input type="date" id="spent_at" name="spent_at" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Receipt Path</label>
                        <input type="text" id="receipt_path" name="receipt_path" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Path to receipt file">
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    <button type="submit" id="submit-form-btn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                        Save Expense
                    </button>
                    <button type="button" id="cancel-form-btn-2" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Expenses List Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">My Expenses</h2>
                <button id="new-expense-btn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                    + New Expense
                </button>
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
                No expenses found. Click "New Expense" to create one.
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.auth.checkAuthOnLoad();

            // State
            let currentFilters = {};
            let currentPage = 1;
            let editingExpenseId = null;

            // Elements
            const loading = document.getElementById('loading');
            const expensesContainer = document.getElementById('expenses-container');
            const noExpenses = document.getElementById('no-expenses');
            const expensesTbody = document.getElementById('expenses-tbody');
            const pagination = document.getElementById('pagination');
            const messageBar = document.getElementById('message-bar');
            const expenseFormSection = document.getElementById('expense-form-section');
            const expenseForm = document.getElementById('expense-form');
            const formTitle = document.getElementById('form-title');

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
                expensesTbody.innerHTML = expenses.map((expense) => `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${expense.title}</td>
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
                                <button onclick="editExpense(${expense.id})" class="text-blue-600 hover:text-blue-900">Edit</button>
                                ${expense.status === 'draft' ? `<button onclick="submitExpense(${expense.id})" class="text-green-600 hover:text-green-900">Submit</button>` : ''}
                            </div>
                        </td>
                    </tr>
                `).join('');
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

            window.editExpense = async (id) => {
                try {
                    const data = await window.expenses.getExpense(id);
                    const expense = data.data; // ExpenseResource wraps in { data: {...} }

                    editingExpenseId = id;
                    formTitle.textContent = 'Edit Expense';
                    document.getElementById('title').value = expense.title;
                    document.getElementById('amount').value = expense.amount;
                    document.getElementById('currency').value = expense.currency;
                    document.getElementById('category').value = expense.category;
                    document.getElementById('spent_at').value = expense.spent_at;
                    document.getElementById('receipt_path').value = expense.receipt_path || '';

                    expenseFormSection.classList.remove('hidden');
                    expenseFormSection.scrollIntoView({ behavior: 'smooth' });
                } catch (error) {
                    console.error('Failed to load expense:', error);
                    showMessage('Failed to load expense details', 'error');
                }
            };

            window.deleteExpense = async (id) => {
                try {
                    await window.expenses.deleteExpense(id);
                    showMessage('Expense deleted successfully');
                    loadExpenses();
                } catch (error) {
                    console.error('Failed to delete expense:', error);
                    const message = error.message || 'Failed to delete expense';
                    showMessage(message, 'error');
                }
            };

            window.submitExpense = async (id) => {
                try {
                    await window.expenses.submitExpense(id);
                    showMessage('Expense submitted successfully');
                    loadExpenses();
                } catch (error) {
                    console.error('Failed to submit expense:', error);
                    const message = error.message || 'Failed to submit expense';
                    showMessage(message, 'error');
                }
            };

            // New expense button
            document.getElementById('new-expense-btn').addEventListener('click', () => {
                editingExpenseId = null;
                formTitle.textContent = 'New Expense';
                expenseForm.reset();
                document.getElementById('currency').value = 'EUR';
                expenseFormSection.classList.remove('hidden');
                expenseFormSection.scrollIntoView({ behavior: 'smooth' });
            });

            // Cancel form buttons
            [document.getElementById('cancel-form-btn'), document.getElementById('cancel-form-btn-2')].forEach(btn => {
                btn.addEventListener('click', () => {
                    expenseFormSection.classList.add('hidden');
                    expenseForm.reset();
                    editingExpenseId = null;
                });
            });

            // Submit form
            expenseForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const data = {
                    title: document.getElementById('title').value,
                    amount: parseFloat(document.getElementById('amount').value),
                    currency: document.getElementById('currency').value,
                    category: document.getElementById('category').value,
                    spent_at: document.getElementById('spent_at').value,
                    receipt_path: document.getElementById('receipt_path').value || null,
                };

                try {
                    if (editingExpenseId) {
                        await window.expenses.updateExpense(editingExpenseId, data);
                        showMessage('Expense updated successfully');
                    } else {
                        await window.expenses.createExpense(data);
                        showMessage('Expense created successfully');
                    }

                    expenseFormSection.classList.add('hidden');
                    expenseForm.reset();
                    editingExpenseId = null;
                    loadExpenses();
                } catch (error) {
                    console.error('Failed to save expense:', error);
                    const message = error.message || (editingExpenseId ? 'Failed to update expense' : 'Failed to create expense');
                    showMessage(message, 'error');
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
            loadExpenses();
        });
    </script>
</body>
</html>

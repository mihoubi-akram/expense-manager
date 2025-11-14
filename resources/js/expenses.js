// Expenses API helper functions
import { fetchWithAuth } from './auth.js';

const API_BASE_URL = '/api';

/**
 * Fetch all expenses with optional filters
 * @param {Object} filters - Filter parameters (status, category, from_date, to_date, page)
 * @returns {Promise<Object>} Paginated expenses list
 */
export async function getExpenses(filters = {}) {
    const queryParams = new URLSearchParams();

    if (filters.status) queryParams.append('status', filters.status);
    if (filters.category) queryParams.append('category', filters.category);
    if (filters.from_date) queryParams.append('from_date', filters.from_date);
    if (filters.to_date) queryParams.append('to_date', filters.to_date);
    if (filters.page) queryParams.append('page', filters.page);

    const url = `${API_BASE_URL}/expenses${queryParams.toString() ? '?' + queryParams.toString() : ''}`;
    const response = await fetchWithAuth(url);

    if (!response.ok) {
        throw await response.json();
    }

    return await response.json();
}

/**
 * Get a single expense by ID
 * @param {number} id - Expense ID
 * @returns {Promise<Object>} Expense data
 */
export async function getExpense(id) {
    const response = await fetchWithAuth(`${API_BASE_URL}/expenses/${id}`);

    if (!response.ok) {
        throw await response.json();
    }

    return await response.json();
}

/**
 * Create a new expense
 * @param {Object} data - Expense data
 * @returns {Promise<Object>} Created expense
 */
export async function createExpense(data) {
    const response = await fetchWithAuth(`${API_BASE_URL}/expenses`, {
        method: 'POST',
        body: JSON.stringify(data),
    });

    if (!response.ok) {
        throw await response.json();
    }

    return await response.json();
}

/**
 * Update an expense
 * @param {number} id - Expense ID
 * @param {Object} data - Updated expense data
 * @returns {Promise<Object>} Updated expense
 */
export async function updateExpense(id, data) {
    const response = await fetchWithAuth(`${API_BASE_URL}/expenses/${id}`, {
        method: 'PUT',
        body: JSON.stringify(data),
    });

    if (!response.ok) {
        throw await response.json();
    }

    return await response.json();
}

/**
 * Delete an expense
 * @param {number} id - Expense ID
 * @returns {Promise<void>}
 */
export async function deleteExpense(id) {
    const response = await fetchWithAuth(`${API_BASE_URL}/expenses/${id}`, {
        method: 'DELETE',
    });

    if (!response.ok && response.status !== 204) {
        throw await response.json();
    }
}

/**
 * Submit an expense for approval
 * @param {number} id - Expense ID
 * @returns {Promise<Object>} Updated expense
 */
export async function submitExpense(id) {
    const response = await fetchWithAuth(`${API_BASE_URL}/expenses/${id}/submit`, {
        method: 'POST',
    });

    if (!response.ok) {
        throw await response.json();
    }

    return await response.json();
}

/**
 * Expense categories
 */
export const EXPENSE_CATEGORIES = {
    MEAL: 'meal',
    TRAVEL: 'travel',
    HOTEL: 'hotel',
    OTHER: 'other',
};

/**
 * Expense statuses
 */
export const EXPENSE_STATUSES = {
    DRAFT: 'draft',
    SUBMITTED: 'submitted',
    APPROVED: 'approved',
    REJECTED: 'rejected',
    PAID: 'paid',
};

/**
 * Get status badge color class
 * @param {string} status - Expense status
 * @returns {string} Tailwind color classes
 */
export function getStatusBadgeClass(status) {
    const classes = {
        [EXPENSE_STATUSES.DRAFT]: 'bg-gray-100 text-gray-800',
        [EXPENSE_STATUSES.SUBMITTED]: 'bg-blue-100 text-blue-800',
        [EXPENSE_STATUSES.APPROVED]: 'bg-green-100 text-green-800',
        [EXPENSE_STATUSES.REJECTED]: 'bg-red-100 text-red-800',
        [EXPENSE_STATUSES.PAID]: 'bg-purple-100 text-purple-800',
    };

    return classes[status] || 'bg-gray-100 text-gray-800';
}

/**
 * Format currency amount
 * @param {number} amount - Amount
 * @param {string} currency - Currency code (default EUR)
 * @returns {string} Formatted currency
 */
export function formatCurrency(amount, currency = 'EUR') {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: currency,
    }).format(amount);
}

/**
 * Format date
 * @param {string} date - ISO date string
 * @returns {string} Formatted date
 */
export function formatDate(date) {
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

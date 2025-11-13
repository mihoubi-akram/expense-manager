// Authentication utilities for API consumption

const API_BASE_URL = '/api';
const TOKEN_KEY = 'auth_token';

// Token management
export function getToken() {
    return localStorage.getItem(TOKEN_KEY);
}

export function setToken(token) {
    localStorage.setItem(TOKEN_KEY, token);
}

export function removeToken() {
    localStorage.removeItem(TOKEN_KEY);
}

export function isAuthenticated() {
    return !!getToken();
}

// Fetch wrapper with authentication
export async function fetchWithAuth(url, options = {}) {
    const token = getToken();
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const config = {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers,
        },
    };

    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }

    if (csrfToken) {
        config.headers['X-CSRF-TOKEN'] = csrfToken;
    }

    const response = await fetch(url, config);

    // If unauthorized, clear token and redirect to login
    if (response.status === 401) {
        removeToken();
        window.location.href = '/login';
        throw new Error('Unauthorized');
    }

    return response;
}

// API methods
export async function login(email, password) {
    const response = await fetchWithAuth(`${API_BASE_URL}/login`, {
        method: 'POST',
        body: JSON.stringify({ email, password }),
    });

    if (!response.ok) {
        const error = await response.json();
        throw error;
    }

    const data = await response.json();
    setToken(data.token);
    return data;
}

export async function register(name, email, password, passwordConfirmation) {
    const response = await fetchWithAuth(`${API_BASE_URL}/register`, {
        method: 'POST',
        body: JSON.stringify({
            name,
            email,
            password,
            password_confirmation: passwordConfirmation,
        }),
    });

    if (!response.ok) {
        const error = await response.json();
        throw error;
    }

    const data = await response.json();
    setToken(data.token);
    return data;
}

export async function logout() {
    try {
        await fetchWithAuth(`${API_BASE_URL}/logout`, {
            method: 'POST',
        });
    } finally {
        removeToken();
    }
}

export async function getCurrentUser() {
    const response = await fetchWithAuth(`${API_BASE_URL}/user`);

    if (!response.ok) {
        throw new Error('Failed to fetch user');
    }

    return await response.json();
}

// Check authentication on protected pages
export function checkAuthOnLoad() {
    if (!isAuthenticated()) {
        window.location.href = '/login';
    }
}

// Redirect if already authenticated (for login/register pages)
export function redirectIfAuthenticated() {
    if (isAuthenticated()) {
        window.location.href = '/dashboard';
    }
}

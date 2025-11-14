import './bootstrap';
import * as auth from './auth.js';
import * as expenses from './expenses.js';

// Expose auth and expenses functions globally
window.auth = auth;
window.expenses = expenses;

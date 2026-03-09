/**
 * elderly-care-api.js
 * Drop this into your frontend project and import it in your HTML pages.
 * All methods return a Promise that resolves to the JSON response.
 *
 * Usage:
 *   import api from './elderly-care-api.js';
 *   const result = await api.auth.login('admin', 'Admin@1234');
 */

const BASE_URL = '/elderly-care/api'; // adjust if your folder name is different

async function request(endpoint, method = 'GET', body = null) {
    const options = {
        method,
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',  // sends session cookie
    };
    if (body && method !== 'GET') {
        options.body = JSON.stringify(body);
    }
    const url = endpoint.startsWith('http') ? endpoint : BASE_URL + endpoint;
    const response = await fetch(url, options);
    const data = await response.json();
    if (!response.ok && !data.success) {
        throw new Error(data.message || `HTTP ${response.status}`);
    }
    return data;
}

const api = {
    // ── AUTH ──────────────────────────────────────────────────
    auth: {
        login: (username, password) =>
            request('/auth/login.php', 'POST', { username, password }),
        logout: () =>
            request('/auth/logout.php', 'POST'),
        session: () =>
            request('/auth/session.php'),
    },

    // ── PROFILE ───────────────────────────────────────────────
    profile: {
        get: (userId = null) =>
            request(userId ? `/profile/profile.php?id=${userId}` : '/profile/profile.php'),
        update: (profileData) =>
            request('/profile/profile.php', 'PUT', profileData),
        changePassword: (currentPassword, newPassword) =>
            request('/profile/profile.php?change_password=1', 'POST', {
                current_password: currentPassword,
                new_password: newPassword,
            }),
    },

    // ── MEDICINES ─────────────────────────────────────────────
    medicines: {
        list: (userId = null) =>
            request(userId ? `/medicines/medicines.php?user_id=${userId}` : '/medicines/medicines.php'),
        add: (medicineData) =>
            request('/medicines/medicines.php', 'POST', medicineData),
        update: (id, medicineData) =>
            request(`/medicines/medicines.php?id=${id}`, 'PUT', medicineData),
        remove: (id) =>
            request(`/medicines/medicines.php?id=${id}`, 'DELETE'),
        markTaken: (medicineId, scheduledTime = null, notes = null) =>
            request('/medicines/medicines.php?action=taken', 'POST', {
                medicine_id: medicineId,
                scheduled_time: scheduledTime || new Date().toISOString().slice(0, 19).replace('T', ' '),
                notes,
            }),
        markMissed: (medicineId, scheduledTime = null, notes = null) =>
            request('/medicines/medicines.php?action=missed', 'POST', {
                medicine_id: medicineId,
                scheduled_time: scheduledTime || new Date().toISOString().slice(0, 19).replace('T', ' '),
                notes,
            }),
        log: (userId = null, limit = 20) =>
            request(userId
                ? `/medicines/medicines.php?action=log&user_id=${userId}&limit=${limit}`
                : `/medicines/medicines.php?action=log&limit=${limit}`),
    },

    // ── HEALTH ────────────────────────────────────────────────
    health: {
        list: (userId = null, type = null, limit = 3) => {
            let url = '/health/health.php?';
            if (userId) url += `user_id=${userId}&`;
            if (type)   url += `type=${type}&`;
            url += `limit=${limit}`;
            return request(url);
        },
        listAll: (userId = null, type = null) => {
            let url = '/health/health.php?all=1';
            if (userId) url += `&user_id=${userId}`;
            if (type)   url += `&type=${type}`;
            return request(url);
        },
        add: (readingData) =>
            request('/health/health.php', 'POST', readingData),
        update: (id, readingData) =>
            request(`/health/health.php?id=${id}`, 'PUT', readingData),
        delete: (id) =>
            request(`/health/health.php?id=${id}`, 'DELETE'),
    },

    // ── CHAT ──────────────────────────────────────────────────
    chat: {
        conversations: () =>
            request('/chat/chat.php'),
        messages: (withUserId, limit = 50, offset = 0) =>
            request(`/chat/chat.php?conversation_with=${withUserId}&limit=${limit}&offset=${offset}`),
        send: (receiverId, message, messageType = 'text') =>
            request('/chat/chat.php', 'POST', { receiver_id: receiverId, message, message_type: messageType }),
        markRead: (messageId = null, senderId = null) =>
            request(messageId ? `/chat/chat.php?id=${messageId}` : '/chat/chat.php', 'PUT',
                senderId ? { sender_id: senderId } : {}),
    },

    // ── SOS ───────────────────────────────────────────────────
    sos: {
        trigger: (message = 'Emergency SOS triggered', alertType = 'sos', lat = null, lng = null) =>
            request('/sos/sos.php', 'POST', {
                message, alert_type: alertType, latitude: lat, longitude: lng,
            }),
        list: (status = null, limit = 20) =>
            request(`/sos/sos.php?limit=${limit}${status ? '&status=' + status : ''}`),
        get: (id) =>
            request(`/sos/sos.php?id=${id}`),
        acknowledge: (id) =>
            request(`/sos/sos.php?id=${id}`, 'PUT', { status: 'acknowledged' }),
        resolve: (id, notes = '') =>
            request(`/sos/sos.php?id=${id}`, 'PUT', { status: 'resolved', resolution_notes: notes }),
    },

    // ── USERS (admin/caregiver) ───────────────────────────────
    users: {
        list: (role = null) =>
            request(role ? `/users/users.php?role=${role}` : '/users/users.php'),
        create: (userData) =>
            request('/users/users.php', 'POST', userData),
        update: (id, userData) =>
            request(`/users/users.php?id=${id}`, 'PUT', userData),
        deactivate: (id) =>
            request(`/users/users.php?id=${id}`, 'DELETE'),
    },

    // ── ASSIGNMENTS ───────────────────────────────────────────
    assignments: {
        list: () =>
            request('/users/assignments.php'),
        assign: (userId, caregiverId) =>
            request('/users/assignments.php', 'POST', { user_id: userId, caregiver_id: caregiverId }),
        remove: (assignmentId) =>
            request(`/users/assignments.php?id=${assignmentId}`, 'DELETE'),
    },
};

export default api;

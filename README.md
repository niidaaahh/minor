# 🏥 Elderly Care App — PHP + MySQL Backend

A complete, ready-to-run backend for your Elderly Care web app. Designed for XAMPP and drop-in integration with your existing HTML/CSS/JS frontend.

---

## 📁 Project Structure

```
elderly-care/                      ← drop into htdocs/
├── api/
│   ├── auth/
│   │   ├── login.php              ← POST  /api/auth/login.php
│   │   ├── logout.php             ← POST  /api/auth/logout.php
│   │   └── session.php            ← GET   /api/auth/session.php
│   ├── profile/
│   │   └── profile.php            ← GET/PUT profile, POST change_password
│   ├── medicines/
│   │   └── medicines.php          ← GET/POST/PUT/DELETE + mark taken/missed
│   ├── health/
│   │   └── health.php             ← GET/POST/PUT/DELETE health readings
│   ├── chat/
│   │   └── chat.php               ← GET conversations/messages, POST send
│   ├── sos/
│   │   └── sos.php                ← POST trigger, GET list, PUT resolve
│   └── users/
│       ├── users.php              ← Admin: manage user accounts
│       └── assignments.php        ← Admin: assign caregivers to users
├── config/
│   ├── database.php               ← DB credentials (edit this)
│   └── helpers.php                ← CORS, JSON response helpers
├── middleware/
│   └── auth.php                   ← Session & role-based auth
├── database.sql                   ← Full DB schema + seed data structure
├── setup_seed.php                 ← One-time setup: creates demo accounts
└── elderly-care-api.js            ← Frontend JS client (copy to your frontend)
```

---

## ⚡ Quick Setup (5 steps)

### Step 1 — Start XAMPP
Open XAMPP Control Panel and start **Apache** and **MySQL**.

### Step 2 — Create the Database
1. Open your browser → go to `http://localhost/phpmyadmin`
2. Click **Import** in the top menu
3. Choose the file `database.sql` from this package
4. Click **Go**

You should see: *"Import has been successfully finished"*

### Step 3 — Place Files in htdocs
Copy the entire `elderly-care` folder to:
```
C:\xampp\htdocs\elderly-care\        (Windows)
/Applications/XAMPP/htdocs/elderly-care/   (Mac)
/opt/lampp/htdocs/elderly-care/      (Linux)
```

### Step 4 — Configure Database (if needed)
Open `config/database.php` and verify:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // ← Change if you set a MySQL password
define('DB_NAME', 'elderly_care_db');
```

### Step 5 — Run Seed Setup
Open your browser and visit:
```
http://localhost/elderly-care/setup_seed.php?key=setup2024
```

This creates the three demo accounts. After it runs successfully:
> ⚠️ **Delete `setup_seed.php`** from your server for security.

---

## 👤 Demo Accounts

| Role      | Username     | Password        | Redirects to            |
|-----------|--------------|-----------------|-------------------------|
| Admin     | `admin`      | `Admin@1234`    | `admin-dashboard.html`  |
| Caregiver | `caregiver1` | `Caregiver@1234`| `caregiver-home.html`   |
| User      | `user1`      | `User@1234`     | `user-management.html`  |

---

## 🔌 Integrating with Your Frontend

### Option A — Use the JS API client (recommended)
Copy `elderly-care-api.js` into your frontend project folder:

```html
<script type="module">
  import api from './elderly-care-api.js';

  // Login example
  const result = await api.auth.login('user1', 'User@1234');
  if (result.success) {
    window.location.href = result.redirect;
  }
</script>
```

### Option B — Direct fetch() calls
```javascript
const response = await fetch('/elderly-care/api/auth/login.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  credentials: 'include',
  body: JSON.stringify({ username: 'user1', password: 'User@1234' })
});
const data = await response.json();
```

> **Important:** Always include `credentials: 'include'` in every fetch call to send the session cookie.

---

## 📡 API Reference

### Auth
| Method | Endpoint | Body | Description |
|--------|----------|------|-------------|
| POST | `/api/auth/login.php` | `{username, password}` | Login, returns redirect URL |
| POST | `/api/auth/logout.php` | — | Destroy session |
| GET  | `/api/auth/session.php` | — | Check active session |

### Profile
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET  | `/api/profile/profile.php` | Get own profile |
| GET  | `/api/profile/profile.php?id=3` | Get user profile (admin/caregiver) |
| PUT  | `/api/profile/profile.php` | Update profile |
| POST | `/api/profile/profile.php?change_password=1` | Change password |

### Medicines
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET  | `/api/medicines/medicines.php` | List active medicines |
| POST | `/api/medicines/medicines.php` | Add medicine (caregiver/admin) |
| PUT  | `/api/medicines/medicines.php?id=1` | Update medicine |
| DELETE | `/api/medicines/medicines.php?id=1` | Deactivate medicine |
| POST | `/api/medicines/medicines.php?action=taken` | Mark dose as taken |
| POST | `/api/medicines/medicines.php?action=missed` | Mark dose as missed |
| GET  | `/api/medicines/medicines.php?action=log` | Get dose history |

### Health Readings
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET  | `/api/health/health.php` | Last 3 readings |
| GET  | `/api/health/health.php?limit=10&type=blood_pressure` | Filtered readings |
| GET  | `/api/health/health.php?all=1` | All readings |
| POST | `/api/health/health.php` | Add new reading |
| PUT  | `/api/health/health.php?id=5` | Update reading |
| DELETE | `/api/health/health.php?id=5` | Delete reading |

**Valid reading types:** `blood_pressure`, `blood_sugar`, `weight`, `temperature`, `heart_rate`, `oxygen_level`

**Blood pressure body example:**
```json
{
  "reading_type": "blood_pressure",
  "systolic": 120,
  "diastolic": 80,
  "unit": "mmHg",
  "notes": "Morning reading"
}
```

### Chat
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET  | `/api/chat/chat.php` | List conversations |
| GET  | `/api/chat/chat.php?conversation_with=2` | Get messages with user |
| POST | `/api/chat/chat.php` | Send message `{receiver_id, message}` |
| PUT  | `/api/chat/chat.php?id=10` | Mark message as read |

### SOS Alerts
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/sos/sos.php` | Trigger SOS alert |
| GET  | `/api/sos/sos.php` | List all alerts |
| GET  | `/api/sos/sos.php?status=active` | Filter by status |
| PUT  | `/api/sos/sos.php?id=1` | Acknowledge / resolve alert |

**SOS body example:**
```json
{
  "alert_type": "sos",
  "message": "I need help!",
  "latitude": 10.8505,
  "longitude": 76.2711
}
```

### Users & Assignments (Admin / Caregiver)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET  | `/api/users/users.php` | List all users |
| GET  | `/api/users/users.php?role=caregiver` | Filter by role |
| POST | `/api/users/users.php` | Create user (admin) |
| PUT  | `/api/users/users.php?id=3` | Update user |
| DELETE | `/api/users/users.php?id=3` | Deactivate user (admin) |
| GET  | `/api/users/assignments.php` | List caregiver assignments |
| POST | `/api/users/assignments.php` | Assign caregiver `{user_id, caregiver_id}` |
| DELETE | `/api/users/assignments.php?id=1` | Remove assignment |

---

## 🔒 Security Notes

- All passwords are stored as **bcrypt hashes** — never plain text.
- Sessions use `httponly` cookies — inaccessible to JavaScript XSS.
- Role checks are enforced server-side on every API call.
- Prepared statements prevent SQL injection throughout.
- For production, set `'secure' => true` in `middleware/auth.php` and use HTTPS.

---

## 🛠️ Troubleshooting

| Problem | Fix |
|---------|-----|
| `Database connection failed` | Check `config/database.php` credentials. Ensure MySQL is running. |
| `Unauthorized` on all requests | Include `credentials: 'include'` in every fetch call. |
| `CORS error` in browser console | Ensure you access via `http://localhost/...`, not `file://...` |
| Login returns 500 error | Import `database.sql` and run `setup_seed.php` first. |
| `Column not found` errors | Re-import `database.sql` to ensure all tables are current. |

---

## 📝 Notes for Production

1. Change `DB_PASS` to a strong MySQL password.
2. Set `'secure' => true` in `middleware/auth.php` and use HTTPS.
3. Remove or restrict `setup_seed.php`.
4. Change all default account passwords immediately.
5. Add rate limiting to `login.php` to prevent brute-force attacks.

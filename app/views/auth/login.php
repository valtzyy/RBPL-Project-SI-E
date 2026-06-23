<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — DealerLink DMS</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: #0b1325;
      font-family: 'Inter', -apple-system, sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 24px;
    }

    /* ── Logo & Title ── */
    .brand-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 24px;
      text-align: center;
    }

    .logo-container {
      width: 64px;
      height: 64px;
      background: #ffffff;
      border-radius: 16px;
      display: flex;
      justify-content: center;
      align-items: center;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
      margin-bottom: 16px;
    }

    .logo-car {
      color: #0b1325;
    }

    .brand-title {
      font-size: 26px;
      font-weight: 800;
      color: #ffffff;
      margin-bottom: 6px;
      letter-spacing: -0.02em;
    }

    .brand-title span {
      font-weight: 400;
      color: #94a3b8;
    }

    .brand-subtitle {
      font-size: 10px;
      font-weight: 700;
      color: #64748b;
      letter-spacing: 0.15em;
      text-transform: uppercase;
    }

    /* ── Login Card ── */
    .login-card {
      background: #ffffff;
      border-radius: 20px;
      width: 100%;
      max-width: 440px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 10px 10px -5px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    .card-body {
      padding: 40px 32px 32px 32px;
    }

    .card-header {
      margin-bottom: 28px;
    }

    .card-title {
      font-size: 22px;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 6px;
    }

    .card-subtitle {
      font-size: 13.5px;
      color: #64748b;
    }

    /* ── Error Box ── */
    .error-alert {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      background: #fef2f2;
      border: 1px solid #fca5a5;
      color: #991b1b;
      padding: 12px 14px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 13px;
      font-weight: 500;
      line-height: 1.4;
    }

    .alert-icon {
      width: 16px;
      height: 16px;
      flex-shrink: 0;
      margin-top: 1px;
    }

    /* ── Form Controls ── */
    .form-group {
      margin-bottom: 20px;
    }

    .form-label-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
    }

    .form-label {
      font-size: 10.5px;
      font-weight: 700;
      color: #64748b;
      letter-spacing: 0.05em;
    }

    .forgot-link {
      font-size: 11px;
      font-weight: 600;
      color: #0f172a;
      text-decoration: none;
    }

    .forgot-link:hover {
      text-decoration: underline;
    }

    .input-wrapper {
      position: relative;
    }

    .form-input {
      width: 100%;
      height: 44px;
      padding: 10px 16px 10px 42px;
      font-family: inherit;
      font-size: 13.5px;
      font-weight: 500;
      color: #0f172a;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      box-sizing: border-box;
      transition: all 0.2s ease;
    }

    .select-input {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' viewBox='0 0 12 7'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.8' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 14px center;
      padding-right: 36px;
      cursor: pointer;
    }

    .form-input:focus {
      outline: none;
      border-color: #94a3b8;
      box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.06);
    }

    .form-input::placeholder {
      color: #94a3b8;
      opacity: 1;
    }

    .input-icon {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      pointer-events: none;
      width: 18px;
      height: 18px;
      transition: color 0.2s ease;
    }

    .left-icon {
      left: 14px;
    }

    .form-input:focus ~ .left-icon {
      color: #0f172a;
    }

    .password-input {
      padding-right: 42px;
    }

    .password-toggle {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      padding: 0;
      cursor: pointer;
      color: #94a3b8;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: color 0.15s ease;
    }

    .password-toggle:hover {
      color: #475569;
    }

    .password-toggle:focus {
      outline: none;
    }

    /* ── Custom Checkbox ── */
    .custom-checkbox {
      display: flex;
      align-items: center;
      position: relative;
      cursor: pointer;
      font-size: 13px;
      user-select: none;
      color: #64748b;
      gap: 10px;
      margin-bottom: 24px;
    }

    .custom-checkbox input {
      position: absolute;
      opacity: 0;
      cursor: pointer;
      height: 0;
      width: 0;
    }

    .checkmark {
      height: 18px;
      width: 18px;
      background-color: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 4px;
      display: inline-block;
      position: relative;
      transition: all 0.2s ease;
      box-sizing: border-box;
    }

    .custom-checkbox:hover input ~ .checkmark {
      border-color: #94a3b8;
    }

    .custom-checkbox input:checked ~ .checkmark {
      background-color: #0f172a;
      border-color: #0f172a;
    }

    .checkmark:after {
      content: "";
      position: absolute;
      display: none;
    }

    .custom-checkbox input:checked ~ .checkmark:after {
      display: block;
    }

    .custom-checkbox .checkmark:after {
      left: 5px;
      top: 2px;
      width: 5px;
      height: 8px;
      border: solid #ffffff;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
    }

    .checkbox-text {
      font-weight: 500;
    }

    /* ── Submit Button ── */
    .btn-submit {
      width: 100%;
      height: 46px;
      background: #000000;
      color: #ffffff;
      border: none;
      border-radius: 8px;
      font-family: inherit;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
      transition: all 0.2s ease;
    }

    .btn-submit:hover {
      background: #1e293b;
    }

    .btn-submit:active {
      transform: scale(0.985);
    }

    .btn-arrow {
      transition: transform 0.2s ease;
    }

    .btn-submit:hover .btn-arrow {
      transform: translateX(3px);
    }

    /* ── Card Footer ── */
    .card-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #f8fafc;
      border-top: 1px solid #f1f5f9;
      padding: 16px 32px;
      font-size: 11px;
      color: #64748b;
    }

    .footer-copyright {
      font-weight: 500;
    }

    .footer-links {
      display: flex;
      gap: 16px;
    }

    .footer-link {
      text-decoration: none;
      color: #64748b;
      font-weight: 600;
      transition: color 0.15s ease;
    }

    .footer-link:hover {
      color: #0f172a;
    }

    /* ── Encrypted Connection Label ── */
    .encrypted-label {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      font-size: 10px;
      font-weight: 700;
      color: #475569;
      margin-top: 24px;
      letter-spacing: 0.05em;
    }

    .lock-small-icon {
      width: 11px;
      height: 11px;
    }
  </style>
</head>
<body>

  <!-- Brand Section -->
  <div class="brand-section">
    <div class="logo-container">
      <svg class="logo-car" width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
        <path d="M19 10h.5c.8 0 1.5.7 1.5 1.5v5.5c0 .6-.4 1-1 1h-1v1.5c0 .8-.7 1.5-1.5 1.5h-1c-.8 0-1.5-.7-1.5-1.5V18H9v1.5c0 .8-.7 1.5-1.5 1.5h-1C5.7 21 5 20.3 5 19.5V18H4c-.6 0-1-.4-1-1v-5.5C3 10.7 3.7 10 4.5 10H5V6c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v4zm-12-4v4h10V6H7zm2.5 9c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5 1.5-.7 1.5-1.5-.7-1.5-1.5-1.5zm7 0c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5 1.5-.7 1.5-1.5-.7-1.5-1.5-1.5z"/>
      </svg>
    </div>
    <h1 class="brand-title">DealerLink <span>DMS</span></h1>
    <div class="brand-subtitle">Enterprise Management System</div>
  </div>

  <!-- Login Card -->
  <div class="login-card">
    <div class="card-body">
      <div class="card-header">
        <h2 class="card-title">Selamat Datang</h2>
        <p class="card-subtitle">Masuk ke akun operasional Anda</p>
      </div>

      <!-- PHP Error Alert -->
      <?php if (!empty($error)): ?>
        <div class="error-alert">
          <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <div class="alert-message"><?= htmlspecialchars($error) ?></div>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form method="POST" action="/login">
        
        <!-- Mock Role Picker -->
        <div class="form-group">
          <div class="form-label-row">
            <label for="mock_role" class="form-label">LOGIN MOCK SEBAGAI ROLE</label>
          </div>
          <div class="input-wrapper">
            <select id="mock_role" name="mock_role" class="form-input select-input">
              <option value="">-- Gunakan Login Database Asli --</option>
              <option value="admin">Admin</option>
              <option value="sales">Sales</option>
              <option value="finance">Finance</option>
              <option value="service advisor">Service Advisor</option>
              <option value="mekanik">Mekanik</option>
              <option value="manager">Manager</option>
            </select>
            <svg class="input-icon left-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
        </div>

        <!-- Input Username/Email -->
        <div class="form-group">
          <div class="form-label-row">
            <label for="identity" class="form-label">EMAIL ATAU USERNAME</label>
          </div>
          <div class="input-wrapper">
            <input 
              type="text" 
              id="identity" 
              name="identity" 
              required 
              autocomplete="username" 
              placeholder="nama@dealerlink.com" 
              class="form-input"
              value="<?= isset($_POST['identity']) ? htmlspecialchars($_POST['identity']) : '' ?>"
            >
            <svg class="input-icon left-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </div>
        </div>

        <!-- Input Password -->
        <div class="form-group">
          <div class="form-label-row">
            <label for="password" class="form-label">KATA SANDI</label>
            <a href="#" class="forgot-link">Lupa Password?</a>
          </div>
          <div class="input-wrapper">
            <input 
              type="password" 
              id="password" 
              name="password" 
              required 
              autocomplete="current-password" 
              placeholder="••••••••" 
              class="form-input password-input"
            >
            <svg class="input-icon left-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <button type="button" class="password-toggle" id="passwordToggleBtn" aria-label="Tampilkan Password">
              <svg class="input-icon right-icon" id="eyeIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>
        </div>

        <!-- Custom Checkbox -->
        <label class="custom-checkbox">
          <input type="checkbox" name="remember" id="remember">
          <span class="checkmark"></span>
          <span class="checkbox-text">Tetap masuk di perangkat ini</span>
        </label>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit">
          Masuk ke Dashboard
          <svg class="btn-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>

      </form>
    </div>

    <!-- Card Footer -->
    <div class="card-footer">
      <div class="footer-copyright">© 2024 DealerLink DMS</div>
      <div class="footer-links">
        <a href="#" class="footer-link">Bantuan</a>
        <a href="#" class="footer-link">Privasi</a>
      </div>
    </div>
  </div>

  <!-- Encrypted Connection Status -->
  <div class="encrypted-label">
    <svg class="lock-small-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
      <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
    </svg>
    AES-256 ENCRYPTED CONNECTION
  </div>

  <!-- JavaScript for Password Toggle -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const passwordInput = document.getElementById('password');
      const toggleBtn = document.getElementById('passwordToggleBtn');
      const eyeIcon = document.getElementById('eyeIcon');

      // SVG path strings for open and closed eye
      const eyeOpenPath = `
        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
        <circle cx="12" cy="12" r="3"></circle>
      `;

      const eyeClosedPath = `
        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
        <line x1="1" y1="1" x2="23" y2="23"></line>
      `;

      toggleBtn.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Update SVG icon path
        if (type === 'password') {
          eyeIcon.innerHTML = eyeOpenPath;
          toggleBtn.setAttribute('aria-label', 'Tampilkan Password');
        } else {
          eyeIcon.innerHTML = eyeClosedPath;
          toggleBtn.setAttribute('aria-label', 'Sembunyikan Password');
        }
      });

      // Autofill on Mock Role Change
      const mockRoleSelect = document.getElementById('mock_role');
      const identityInput = document.getElementById('identity');
      const passwordInputEl = document.getElementById('password');

      mockRoleSelect.addEventListener('change', function () {
        const val = this.value;
        if (val) {
          identityInput.value = val.replace(/\s+/g, '_') + '@dealerlink.com';
          passwordInputEl.value = 'password';
        } else {
          identityInput.value = '';
          passwordInputEl.value = '';
        }
      });
    });
  </script>
</body>
</html>

let currentStep = 1;
let generatedOTP = '';
let verifiedEmail = '';
let countdownInterval = null;

// ─── Utility: Toast ──────────────────────────
function showToast(icon, title, sub, duration = 3000) {
  const toast = document.getElementById('toast');
  document.getElementById('toastIcon').textContent = icon;
  document.getElementById('toastTitle').textContent = title;
  document.getElementById('toastSub').textContent = sub;
  toast.style.display = 'flex';
  toast.classList.add('show');
  clearTimeout(toast._timer);
  toast._timer = setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => (toast.style.display = 'none'), 400);
  }, duration);
}

// ─── Utility: Error messages ─────────────────
function showError(id, msg) {
  const el = document.getElementById(id);
  if (msg) el.textContent = msg;
  el.style.display = 'block';
}
function hideError(id) {
  document.getElementById(id).style.display = 'none';
}

// ─── Navigate between views ──────────────────
function goToStep(step) {
  document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
  document.getElementById('view' + step).classList.add('active');
  currentStep = step;

  // Update step dots
  document.querySelectorAll('.step-dot').forEach((dot, i) => {
    dot.classList.toggle('active', i < step);
    dot.classList.toggle('completed', i < step - 1);
  });

  // Update step label
  const labels = ['Step 1 of 3', 'Step 2 of 3', 'Step 3 of 3', 'Complete'];
  const labelEl = document.getElementById('stepLabel');
  if (labelEl) labelEl.textContent = labels[step - 1] || '';

  document.querySelectorAll('.art-step').forEach((s, i) => {
    s.classList.toggle('active', i === step - 1);
  });

  const indicator = document.getElementById('stepIndicator');
  if (indicator) indicator.style.display = step === 4 ? 'none' : '';
}

function goToLogin() {
  window.location.href = './login';
}

// ─── STEP 1: Send Code ───────────────────────
function handleSendCode() {
  const emailInput = document.getElementById('emailInput');
  const email = emailInput.value.trim();
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  hideError('emailError');
  if (!email || !emailRegex.test(email)) {
    showError('emailError', 'Please enter a valid email address.');
    emailInput.focus();
    return;
  }
  const btn = document.getElementById('sendCodeBtn');
  setLoading(btn, true);
  setTimeout(() => {
    setLoading(btn, false);
    verifiedEmail = email;
    generatedOTP = Math.floor(100000 + Math.random() * 900000).toString();
    console.info('[DEV ONLY] OTP:', generatedOTP);
    document.getElementById('emailDisplay').textContent = email;
    document.getElementById('successEmail').textContent = email;
    goToStep(2);
    startCountdown();
    clearOTP();
    showToast('📧', 'Code Sent!', `Check ${email} for your 6-digit code.`);
  }, 1200);
}

// ─── STEP 2: Verify OTP ──────────────────────
function handleVerify() {
  const inputs = document.querySelectorAll('.otp-input');
  const code = Array.from(inputs).map(i => i.value).join('');
  hideError('otpError');

  if (code.length < 6) {
    showError('otpError', 'Please enter all 6 digits.');
    inputs[0].focus();
    return;
  }

  const btn = document.getElementById('verifyBtn');
  setLoading(btn, true);

  setTimeout(() => {
    setLoading(btn, false);
    if (code === generatedOTP) {
      stopCountdown();
      goToStep(3);
      showToast('✅', 'Verified!', 'Code accepted. Set your new password.');
    } else {
      showError('otpError', 'Incorrect code. Please try again.');
      inputs.forEach(i => { i.value = ''; i.classList.add('error-shake'); });
      setTimeout(() => inputs.forEach(i => i.classList.remove('error-shake')), 600);
      inputs[0].focus();
    }
  }, 900);
}

// ─── STEP 2: Resend ──────────────────────────
function handleResend() {
  generatedOTP = Math.floor(100000 + Math.random() * 900000).toString();
  console.info('[DEV ONLY] New OTP:', generatedOTP); // Remove in production
  clearOTP();
  hideError('otpError');
  stopCountdown();
  startCountdown();
  showToast('🔁', 'Code Resent', `A new code was sent to ${verifiedEmail}.`);
}

// ─── Countdown timer ─────────────────────────
function startCountdown(seconds = 59) {
  const countdownEl = document.getElementById('countdown');
  const resendBtn = document.getElementById('resendBtn');
  resendBtn.disabled = true;
  let remaining = seconds;

  countdownEl.textContent = `0:${String(remaining).padStart(2, '0')}`;

  countdownInterval = setInterval(() => {
    remaining--;
    countdownEl.textContent = `0:${String(remaining).padStart(2, '0')}`;
    if (remaining <= 0) {
      stopCountdown();
      countdownEl.textContent = '';
      resendBtn.disabled = false;
    }
  }, 1000);
}

function stopCountdown() {
  if (countdownInterval) {
    clearInterval(countdownInterval);
    countdownInterval = null;
  }
}

// ─── Clear OTP inputs ────────────────────────
function clearOTP() {
  document.querySelectorAll('.otp-input').forEach(i => (i.value = ''));
  const first = document.querySelector('.otp-input');
  if (first) setTimeout(() => first.focus(), 100);
}

// ─── STEP 3: Reset Password ──────────────────
function handleReset() {
  const newPass = document.getElementById('newPass').value;
  const confirmPass = document.getElementById('confirmPass').value;
  hideError('newPassError');
  hideError('confirmPassError');

  let valid = true;

  if (newPass.length < 8) {
    showError('newPassError', 'Password must be at least 8 characters.');
    valid = false;
  }

  if (newPass !== confirmPass) {
    showError('confirmPassError', 'Passwords do not match.');
    valid = false;
  }

  if (!valid) return;

  const btn = document.getElementById('resetBtn');
  setLoading(btn, true);

  // Simulate password reset API call
  setTimeout(() => {
    setLoading(btn, false);
    goToStep(4);
    showToast('🔥', 'Password Updated!', 'You can now sign in with your new password.');
    startRedirectBar();
  }, 1200);
}

// ─── Success redirect bar ────────────────────
function startRedirectBar() {
  const fill = document.querySelector('.redirect-fill');
  if (fill) {
    fill.style.transition = 'width 4s linear';
    setTimeout(() => (fill.style.width = '100%'), 50);
    setTimeout(goToLogin, 4200);
  }
}

// ─── Password Strength ───────────────────────
function checkStrength() {
  const val = document.getElementById('newPass').value;
  const segs = [
    document.getElementById('seg1'),
    document.getElementById('seg2'),
    document.getElementById('seg3'),
    document.getElementById('seg4'),
  ];
  const label = document.getElementById('strengthLabel');

  let score = 0;
  if (val.length >= 8) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  const levels = [
    { color: '', text: 'Enter a password' },
    { color: '#e74c3c', text: 'Weak' },
    { color: '#e67e22', text: 'Fair' },
    { color: '#f1c40f', text: 'Good' },
    { color: '#2ecc71', text: 'Strong' },
  ];

  const level = val.length === 0 ? 0 : score;

  segs.forEach((seg, i) => {
    seg.style.background = i < level ? levels[level].color : '';
    seg.style.opacity = i < level ? '1' : '0.2';
  });

  label.textContent = levels[level].text;
  label.style.color = levels[level].color || '';
}

// ─── Toggle Password Visibility ──────────────
function togglePass(inputId, eyeId) {
  const input = document.getElementById(inputId);
  const eye = document.getElementById(eyeId);
  const isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';
  eye.textContent = isHidden ? '🙈' : '👁';
}

// ─── Loading state helper ────────────────────
function setLoading(btn, loading) {
  const textEl = btn.querySelector('.btn-text');
  if (loading) {
    btn.disabled = true;
    btn.classList.add('loading');
    if (textEl) textEl.textContent = 'PLEASE WAIT...';
  } else {
    btn.disabled = false;
    btn.classList.remove('loading');
    // Restore original text
    const texts = {
      sendCodeBtn: 'SEND RECOVERY CODE',
      verifyBtn: 'VERIFY CODE',
      resetBtn: 'RESET PASSWORD',
    };
    if (textEl && texts[btn.id]) textEl.textContent = texts[btn.id];
  }
}

// ─── OTP Input Behaviour ─────────────────────
document.addEventListener('DOMContentLoaded', () => {
  goToStep(1); // Initialize active states

  const otpInputs = document.querySelectorAll('.otp-input');

  otpInputs.forEach((input, idx) => {
    // Auto-advance on digit entry
    input.addEventListener('input', (e) => {
      const val = e.target.value.replace(/\D/g, '');
      e.target.value = val.slice(-1); // keep only last digit
      if (val && idx < otpInputs.length - 1) {
        otpInputs[idx + 1].focus();
      }
    });

    // Backspace: clear current and move back
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !input.value && idx > 0) {
        otpInputs[idx - 1].value = '';
        otpInputs[idx - 1].focus();
      }
    });

    // Allow only numeric keys
    input.addEventListener('keypress', (e) => {
      if (!/[0-9]/.test(e.key)) e.preventDefault();
    });
  });

  // Paste: distribute digits across OTP inputs
  document.getElementById('otpGroup').addEventListener('paste', (e) => {
    e.preventDefault();
    const pasted = (e.clipboardData || window.clipboardData)
      .getData('text')
      .replace(/\D/g, '')
      .slice(0, 6);
    otpInputs.forEach((input, i) => (input.value = pasted[i] || ''));
    const nextEmpty = Array.from(otpInputs).findIndex(i => !i.value);
    (nextEmpty >= 0 ? otpInputs[nextEmpty] : otpInputs[5]).focus();
  });

  // Allow Enter key to submit on focused view
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Enter') return;
    if (currentStep === 1) handleSendCode();
    else if (currentStep === 2) handleVerify();
    else if (currentStep === 3) handleReset();
  });
});
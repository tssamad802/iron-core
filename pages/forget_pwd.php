<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>IronCore Gym — Forgot Password</title>
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap"
    rel="stylesheet" />
  <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
</head>

<body>

  <!-- ══ LEFT: ART PANEL ══ -->
  <div class="art-panel">
    <div class="art-grid"></div>
    <div class="art-glow"></div>
    <div class="orb orb-a"></div>
    <div class="orb orb-b"></div>

    <div class="art-brand anim-fade-up">
      <div class="main-logo">IRON<span>CORE</span></div>
      <div class="sub-label">Password Recovery</div>
    </div>

    <!-- Recovery steps -->
    <div class="art-steps anim-fade-up anim-d2">
      <div class="art-step" id="artStep1">
        <div class="step-num">1</div>
        <div class="step-info">
          <div class="title">Enter Email</div>
          <div class="desc">Provide your admin email address to begin recovery</div>
        </div>
      </div>
      <div class="art-step" id="artStep2">
        <div class="step-num">2</div>
        <div class="step-info">
          <div class="title">Verify Code</div>
          <div class="desc">Enter the 6-digit code sent to your inbox</div>
        </div>
      </div>
      <div class="art-step" id="artStep3">
        <div class="step-num">3</div>
        <div class="step-info">
          <div class="title">New Password</div>
          <div class="desc">Set a strong new password for your account</div>
        </div>
      </div>
    </div>

    <div class="art-footer">
      <div class="rule"></div>
      <span>Secure Recovery</span>
      <div class="rule flip"></div>
    </div>
  </div>

  <!-- ══ RIGHT: FORM PANEL ══ -->
  <div class="form-panel">

    <!-- ── STEP INDICATOR ── -->
    <div class="step-indicator anim-fade-up" id="stepIndicator">
      <div class="step-dot active" id="dot1"></div>
      <div class="step-dot" id="dot2"></div>
      <div class="step-dot" id="dot3"></div>
      <span class="step-label" id="stepLabel">Step 1 of 3</span>
    </div>

    <!-- ════ VIEW 1: EMAIL ════ -->
    <div class="view active" id="view1">
      <div class="secure-badge anim-fade-up anim-d1">
        <div class="pulse-dot"></div>
        <span>Password Recovery</span>
      </div>

      <div class="form-title anim-fade-up anim-d2">FORGOT<br />PASSWORD?</div>
      <p class="form-desc anim-fade-up anim-d3">
        No problem. Enter your admin email and<br />we'll send a verification code instantly.
      </p>

      <div class="form-group anim-fade-up anim-d3">
        <label class="form-label" for="emailInput">Admin Email</label>
        <div class="input-wrap">
          <span class="input-icon"><i class="fa-regular fa-envelope"></i></span>
          <input class="form-input" type="email" id="emailInput" placeholder="admin@ironcore.gym"
            autocomplete="email" />
        </div>
        <div class="error-msg" id="emailError">Please enter a valid email address.</div>
      </div>

      <button class="btn-submit anim-fade-up anim-d4" id="sendCodeBtn" onclick="handleSendCode()">
        <span class="btn-text">SEND RECOVERY CODE</span>
      </button>

      <button class="back-link anim-fade-up anim-d5" onclick="goToLogin()">
        <i class="fa-solid fa-arrow-left"></i>
        Back to Sign In
      </button>
    </div>

    <!-- ════ VIEW 2: OTP ════ -->
    <div class="view" id="view2">
      <div class="secure-badge anim-fade-up">
        <div class="pulse-dot"></div>
        <span>Code Verification</span>
      </div>

      <div class="form-title anim-fade-up anim-d1">VERIFY<br />CODE.</div>
      <p class="form-desc anim-fade-up anim-d2">
        We sent a 6-digit code to<br />
        <strong id="emailDisplay" style="color: var(--accent);"></strong>
      </p>

      <div class="form-group anim-fade-up anim-d2">
        <label class="form-label">Verification Code</label>
        <div class="otp-group" id="otpGroup">
          <input class="otp-input" type="text" maxlength="1" data-index="0" placeholder="·" />
          <input class="otp-input" type="text" maxlength="1" data-index="1" placeholder="·" />
          <input class="otp-input" type="text" maxlength="1" data-index="2" placeholder="·" />
          <input class="otp-input" type="text" maxlength="1" data-index="3" placeholder="·" />
          <input class="otp-input" type="text" maxlength="1" data-index="4" placeholder="·" />
          <input class="otp-input" type="text" maxlength="1" data-index="5" placeholder="·" />
        </div>
        <div class="error-msg" id="otpError">Incorrect code. Please try again.</div>
      </div>

      <div class="resend-row anim-fade-up anim-d3">
        <span class="resend-text">Didn't receive it?</span>
        <div style="display:flex;align-items:center;gap:8px;">
          <button class="resend-btn" id="resendBtn" onclick="handleResend()" disabled>Resend Code</button>
          <span class="countdown" id="countdown">0:59</span>
        </div>
      </div>

      <button class="btn-submit anim-fade-up anim-d4" id="verifyBtn" onclick="handleVerify()">
        <span class="btn-text">VERIFY CODE</span>
      </button>

      <button class="back-link anim-fade-up anim-d5" onclick="goToStep(1)">
        <i class="fa-solid fa-arrow-left"></i>
        Change email
      </button>
    </div>

    <!-- ════ VIEW 3: NEW PASSWORD ════ -->
    <div class="view" id="view3">
      <div class="secure-badge anim-fade-up">
        <div class="pulse-dot"></div>
        <span>Set New Password</span>
      </div>

      <div class="form-title anim-fade-up anim-d1">NEW<br />PASSWORD.</div>
      <p class="form-desc anim-fade-up anim-d2">
        Choose a strong password to secure<br />your admin account.
      </p>

      <div class="form-group anim-fade-up anim-d2">
        <label class="form-label" for="newPass">New Password</label>
        <div class="input-wrap">
          <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
          <input class="form-input" type="password" id="newPass" placeholder="Enter new password"
            style="padding-right:46px;" oninput="checkStrength()" />
          <span class="input-suffix" id="eye1" onclick="togglePass('newPass','eye1')">👁</span>
        </div>
        <div class="strength-bar" id="strengthBar">
          <div class="strength-seg" id="seg1"></div>
          <div class="strength-seg" id="seg2"></div>
          <div class="strength-seg" id="seg3"></div>
          <div class="strength-seg" id="seg4"></div>
        </div>
        <div class="strength-label" id="strengthLabel">Enter a password</div>
        <div class="error-msg" id="newPassError">Password must be at least 8 characters.</div>
      </div>

      <div class="form-group anim-fade-up anim-d3">
        <label class="form-label" for="confirmPass">Confirm Password</label>
        <div class="input-wrap">
          <span class="input-icon"><i class="fa-solid fa-shield-halved"></i></span>
          <input class="form-input" type="password" id="confirmPass" placeholder="Confirm your password"
            style="padding-right:46px;" />
          <span class="input-suffix" id="eye2" onclick="togglePass('confirmPass','eye2')">👁</span>
        </div>
        <div class="error-msg" id="confirmPassError">Passwords do not match.</div>
      </div>

      <button class="btn-submit anim-fade-up anim-d4" id="resetBtn" onclick="handleReset()">
        <span class="btn-text">RESET PASSWORD</span>
      </button>
    </div>

    <!-- ════ VIEW 4: SUCCESS ════ -->
    <div class="view" id="view4">
      <div class="success-state">
        <div class="success-icon-wrap">
          <div class="success-checkmark">✓</div>
        </div>
        <div class="success-title">PASSWORD<br />UPDATED!</div>
        <p class="success-desc">
          Your password has been reset successfully.<br />
          You can now sign in with your new credentials.
        </p>
        <div class="success-email-chip">
          <i class="fa-solid fa-circle-check"></i>
          <span id="successEmail"></span>
        </div>
        <div class="redirect-bar">
          <div class="redirect-fill"></div>
        </div>
        <p class="redirect-text">Redirecting to Sign In...</p>
        <button class="btn-submit" style="margin-top:20px;" onclick="goToLogin()">
          <span class="btn-text">GO TO SIGN IN</span>
        </button>
      </div>
    </div>

  </div><!-- /form-panel -->

  <!-- Toast -->
  <div class="toast" id="toast" style="display:none;">
    <span class="toast-icon" id="toastIcon">🔥</span>
    <div>
      <div class="toast-title" id="toastTitle"></div>
      <div class="toast-sub" id="toastSub"></div>
    </div>
  </div>

</body>
<script src="./js/forgot_pwd.js"></script>

</html>
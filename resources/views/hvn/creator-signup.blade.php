@extends('hvn.layout')
@section('title', 'Join as Creator — Her Vision Network')

@section('content')
<div class="hvn-form">
    <h1>Join Her Vision Network</h1>
    <p class="sub">Create your account and choose how you'd like to participate.</p>

    <div id="alert" class="alert" style="display:none"></div>

    <form id="signup-form">
        <div class="form-group">
            <label>Email address</label>
            <input type="email" id="email" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" id="password" placeholder="Min. 5 characters" required>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" id="password_confirmation" placeholder="Repeat your password" required>
        </div>

        <div class="form-group">
            <label>I am joining as a…</label>
            <div class="role-options">
                <label class="role-option" id="opt-viewer">
                    <input type="radio" name="role" value="viewer" checked>
                    <strong>👁 Viewer</strong>
                    <span>Browse content, join the community, comment & like</span>
                </label>
                <label class="role-option selected" id="opt-creator">
                    <input type="radio" name="role" value="creator">
                    <strong>🎬 Creator</strong>
                    <span>Upload content, manage a public profile & get discovered</span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn-primary" id="submit-btn">Create Account</button>

        <p style="text-align:center; margin-top:18px; font-size:13px; color:#666;">
            Already have an account? <a href="/login" style="color:#6c63ff;">Sign in</a>
        </p>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Keep role-option UI in sync with radio buttons
    document.querySelectorAll('.role-option').forEach(opt => {
        opt.addEventListener('click', () => {
            document.querySelectorAll('.role-option').forEach(o => o.classList.remove('selected'));
            opt.classList.add('selected');
            opt.querySelector('input[type=radio]').checked = true;
        });
    });

    document.getElementById('signup-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('submit-btn');
        const alert = document.getElementById('alert');
        alert.style.display = 'none';
        btn.disabled = true;
        btn.textContent = 'Creating account…';

        const role = document.querySelector('input[name=role]:checked').value;

        try {
            const res = await fetch('/api/v1/auth/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value,
                    password_confirmation: document.getElementById('password_confirmation').value,
                    role: role,
                    token_name: 'web'
                })
            });

            const data = await res.json();

            if (!res.ok) {
                const msgs = data.errors
                    ? Object.values(data.errors).flat().join('<br>')
                    : (data.message || 'Registration failed. Please try again.');
                alert.className = 'alert alert-error';
                alert.innerHTML = msgs;
                alert.style.display = 'block';
                btn.disabled = false;
                btn.textContent = 'Create Account';
                return;
            }

            if (data.status === 'needs_email_verification') {
                alert.className = 'alert alert-success';
                alert.textContent = 'Account created! Please check your email to verify your account.';
                alert.style.display = 'block';
                btn.textContent = 'Check your email';
                return;
            }

            // Success — store token if returned, then redirect
            if (data.boostrapData && data.boostrapData.token) {
                localStorage.setItem('access_token', data.boostrapData.token);
            }

            window.location.href = role === 'creator' ? '/creator/dashboard' : '/community';

        } catch (err) {
            alert.className = 'alert alert-error';
            alert.textContent = 'Network error. Please try again.';
            alert.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Create Account';
        }
    });
</script>
@endsection

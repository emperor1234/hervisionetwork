@extends('hvn.layout')
@section('title', 'Creator Dashboard — Her Vision Network')

@section('head')
<style>
    .dash-grid { display: grid; grid-template-columns: 280px 1fr; gap: 24px; align-items: start; }

    /* Profile card */
    .dash-profile {
        background: #2a2a2a;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 10px;
        padding: 24px;
        text-align: center;
    }
    .dash-avatar {
        width: 80px; height: 80px; border-radius: 50%;
        background: linear-gradient(135deg, #a83428 0%, #F65F54 100%);
        margin: 0 auto 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 30px; font-weight: 700; color: #fff;
        overflow: hidden;
    }
    .dash-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .dash-name { font-size: 18px; font-weight: 600; color: #fff; margin-bottom: 4px; }
    .dash-email { font-size: 13px; color: #888; margin-bottom: 20px; }
    .dash-bio { font-size: 13px; color: #aaa; line-height: 1.6; margin-bottom: 20px; text-align: left; }
    .dash-edit-btn {
        display: block; width: 100%;
        background: #F65F54; color: #fff;
        border: none; border-radius: 6px;
        padding: 10px; font-size: 14px; font-weight: 500;
        cursor: pointer; text-decoration: none; text-align: center;
        font-family: inherit; transition: background .2s;
    }
    .dash-edit-btn:hover { background: #d94f45; }

    /* Stats row */
    .dash-stats {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #2a2a2a;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 8px;
        padding: 16px;
        text-align: center;
    }
    .stat-card .stat-num { font-size: 28px; font-weight: 700; color: #F65F54; }
    .stat-card .stat-label { font-size: 12px; color: #888; margin-top: 4px; }

    /* Posts table */
    .dash-posts-heading { font-size: 16px; font-weight: 500; color: #ccc; margin-bottom: 14px; }
    .dash-post-row {
        background: #2a2a2a;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 14px;
        text-decoration: none;
        color: inherit;
        transition: border-color .2s;
    }
    .dash-post-row:hover { border-color: #F65F54; }
    .dash-post-title { flex: 1; font-size: 14px; color: #e0e0e0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .dash-post-meta { font-size: 12px; color: #666; white-space: nowrap; }

    .dash-empty { text-align: center; padding: 40px 20px; color: #555; font-size: 14px; }

    /* Edit profile form */
    .profile-form { background: #2a2a2a; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; padding: 24px; margin-top: 16px; }
    .profile-form h3 { font-size: 15px; font-weight: 500; color: #bbb; margin-bottom: 16px; }
    .profile-form input, .profile-form textarea {
        width: 100%; background: #1e1e1e; border: 1px solid #333;
        border-radius: 6px; color: #e0e0e0; padding: 10px 14px;
        font-size: 14px; font-family: inherit; outline: none;
        margin-bottom: 12px; transition: border-color .2s;
    }
    .profile-form input:focus, .profile-form textarea:focus { border-color: #F65F54; }
    .profile-form textarea { resize: vertical; min-height: 80px; }
    #profile-alert { margin-bottom: 10px; }

    @media (max-width: 700px) {
        .dash-grid { grid-template-columns: 1fr; }
        .dash-stats { grid-template-columns: repeat(3, 1fr); }
    }
</style>
@endsection

@section('content')

<div style="margin-bottom:24px;">
    <h1 style="font-size:24px;font-weight:600;color:#fff;">Creator Dashboard</h1>
    <p style="color:#888;font-size:14px;margin-top:4px;">Welcome back, {{ $user->username }}.</p>
</div>

{{-- Stats --}}
@php
    $totalPosts    = $posts->count();
    $totalReplies  = $posts->sum('comments_count');
    $totalLikes    = $posts->sum('likes_count');
@endphp
<div class="dash-stats">
    <div class="stat-card">
        <div class="stat-num">{{ $totalPosts }}</div>
        <div class="stat-label">Posts</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $totalReplies }}</div>
        <div class="stat-label">Replies received</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $totalLikes }}</div>
        <div class="stat-label">Likes received</div>
    </div>
</div>

<div class="dash-grid">
    {{-- Left: profile card --}}
    <div>
        <div class="dash-profile">
            <div class="dash-avatar">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->username }}">
                @else
                    {{ strtoupper(substr($user->username ?? $user->email, 0, 1)) }}
                @endif
            </div>
            <div class="dash-name">{{ optional($profile)->display_name ?? $user->username }}</div>
            <div class="dash-email">{{ $user->email }}</div>
            @if($profile && $profile->bio)
                <div class="dash-bio">{{ $profile->bio }}</div>
            @endif
            <a href="#edit-profile" class="dash-edit-btn" onclick="document.getElementById('profile-form-card').scrollIntoView({behavior:'smooth'});return false;">Edit Profile</a>
        </div>

        {{-- Edit profile form --}}
        <div class="profile-form" id="profile-form-card">
            <h3>Edit Public Profile</h3>
            <div id="profile-alert" class="alert" style="display:none;"></div>
            <input type="text" id="pf-username" placeholder="Username (e.g. jane_doe)" value="{{ e($user->username ?? '') }}">
            <small style="display:block;color:#666;font-size:12px;margin:-8px 0 12px;">Used in your public URL: /creators/<em id="pf-username-preview">{{ $user->username ?? 'username' }}</em></small>
            <input type="text" id="pf-name"    placeholder="Display name" value="{{ e(optional($profile)->display_name ?? '') }}">
            <textarea id="pf-bio" placeholder="Short bio (shown on your creator card)">{{ e(optional($profile)->bio ?? '') }}</textarea>
            <input type="url"  id="pf-website" placeholder="Website URL (https://…)" value="{{ e(optional($profile)->website_url ?? '') }}">
            <input type="text" id="pf-contact" placeholder="Public contact email" value="{{ e(optional($profile)->contact_email ?? '') }}">
            <button class="dash-edit-btn" style="margin-top:4px;" onclick="saveProfile()">Save Profile</button>
        </div>
    </div>

    {{-- Right: recent posts --}}
    <div>
        <div class="dash-posts-heading">Your Community Posts</div>
        @if($posts->isEmpty())
            <div class="dash-empty">You haven't posted in the community yet.<br>
                <a href="/community" style="color:#F65F54;text-decoration:none;">Go to Community →</a>
            </div>
        @else
            @foreach($posts as $post)
            <a href="/community/{{ $post->id }}/{{ Str::slug($post->title) }}" class="dash-post-row">
                <div class="dash-post-title">{{ $post->title }}</div>
                <div class="dash-post-meta">
                    {{ $post->comments_count }} {{ Str::plural('reply', $post->comments_count) }} ·
                    {{ $post->likes_count }} {{ Str::plural('like', $post->likes_count) }} ·
                    {{ $post->created_at->diffForHumans() }}
                </div>
            </a>
            @endforeach
        @endif

        <div style="margin-top:20px;">
            <a href="/community" style="color:#F65F54;font-size:14px;text-decoration:none;">+ Start a new discussion →</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('pf-username').addEventListener('input', function () {
    document.getElementById('pf-username-preview').textContent = this.value.trim() || 'username';
});

async function saveProfile() {
    const alertEl = document.getElementById('profile-alert');
    alertEl.style.display = 'none';

    const payload = {
        username:      document.getElementById('pf-username').value.trim() || undefined,
        display_name:  document.getElementById('pf-name').value.trim() || null,
        bio:           document.getElementById('pf-bio').value.trim() || null,
        website_url:   document.getElementById('pf-website').value.trim() || null,
        contact_email: document.getElementById('pf-contact').value.trim() || null,
    };

    try {
        // Establish Sanctum stateful session and refresh XSRF cookie
        await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });

        // Read XSRF-TOKEN cookie (Laravel URL-encodes it)
        var xsrfCookie = document.cookie.split(';').reduce(function(val, c) {
            var parts = c.trim().split('=');
            return parts[0] === 'XSRF-TOKEN' ? decodeURIComponent(parts[1]) : val;
        }, '');

        const res = await fetch('/api/v1/creator/profile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': xsrfCookie,
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (res.ok) {
            alertEl.className = 'alert alert-success';
            alertEl.textContent = 'Profile updated successfully.';
            alertEl.style.display = 'block';
        } else {
            const msg = data.errors
                ? Object.values(data.errors).flat().join(' ')
                : (data.message || 'Could not save profile.');
            alertEl.className = 'alert alert-error';
            alertEl.textContent = msg;
            alertEl.style.display = 'block';
        }
    } catch {
        alertEl.className = 'alert alert-error';
        alertEl.textContent = 'Network error. Please try again.';
        alertEl.style.display = 'block';
    }
}
</script>
@endsection

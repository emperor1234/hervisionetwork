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

    /* My Content */
    .content-section { margin-bottom: 28px; }
    .content-section-head {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;
    }
    .content-section-head h2 { font-size: 16px; font-weight: 500; color: #ccc; }
    .content-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
    .content-card {
        background: #2a2a2a; border: 1px solid rgba(255,255,255,0.06);
        border-radius: 8px; overflow: hidden;
    }
    .content-card-thumb {
        width: 100%; aspect-ratio: 16/9; background: #1a1a1a;
        display: flex; align-items: center; justify-content: center; color: #333; font-size: 28px;
    }
    .content-card-body { padding: 10px; }
    .content-card-title { font-size: 13px; color: #ddd; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px; }
    .content-card-meta { font-size: 11px; color: #666; margin-bottom: 8px; }
    .content-card-del { font-size: 11px; color: #c0392b; cursor: pointer; background: none; border: none; font-family: inherit; padding: 0; }
    .content-card-del:hover { color: #e74c3c; }

    /* Upload form */
    .upload-panel {
        background: #2a2a2a; border: 1px solid rgba(255,255,255,0.06);
        border-radius: 10px; padding: 20px; margin-bottom: 14px;
    }
    .upload-panel h3 { font-size: 14px; font-weight: 500; color: #bbb; margin-bottom: 14px; }
    .upload-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .upload-panel input, .upload-panel select, .upload-panel textarea {
        width: 100%; background: #1e1e1e; border: 1px solid #333;
        border-radius: 6px; color: #e0e0e0; padding: 9px 12px;
        font-size: 13px; font-family: inherit; outline: none;
        margin-bottom: 10px; transition: border-color .2s;
    }
    .upload-panel input:focus, .upload-panel select:focus, .upload-panel textarea:focus { border-color: #F65F54; }
    .upload-panel select option { background: #1e1e1e; }
    .upload-panel textarea { resize: vertical; min-height: 70px; }
    #upload-alert { margin-bottom: 10px; }

    @media (max-width: 700px) {
        .dash-grid { grid-template-columns: 1fr; }
        .dash-stats { grid-template-columns: repeat(3, 1fr); }
        .upload-row { grid-template-columns: 1fr; }
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

{{-- My Content --}}
<div class="content-section">
    <div class="content-section-head">
        <h2>My Content</h2>
        <button class="dash-edit-btn" style="width:auto;padding:8px 16px;font-size:13px;" onclick="toggleUpload()">+ Upload New</button>
    </div>

    <div id="upload-panel" class="upload-panel" style="display:none;">
        <h3>Upload New Content</h3>
        <div id="upload-alert" class="alert" style="display:none;"></div>
        <input type="text" id="uc-title" placeholder="Title of your content *">
        <div class="upload-row">
            <select id="uc-type">
                <option value="movie">Movie</option>
                <option value="short">Short Film</option>
                <option value="documentary">Documentary</option>
                <option value="series">Series</option>
            </select>
            <input type="number" id="uc-year" placeholder="Year (e.g. 2024)" min="1900" max="2099">
        </div>
        <textarea id="uc-desc" placeholder="Short description (optional)"></textarea>
        <input type="url" id="uc-url" placeholder="Video URL — YouTube, Vimeo or direct link *">
        <div style="display:flex;gap:10px;">
            <button class="dash-edit-btn" style="width:auto;padding:9px 20px;" onclick="uploadContent()">Upload</button>
            <button class="dash-edit-btn" style="width:auto;padding:9px 16px;background:#333;" onclick="toggleUpload()">Cancel</button>
        </div>
    </div>

    @if($myContent->isEmpty())
        <div class="dash-empty">You haven't uploaded any content yet. Click <strong>+ Upload New</strong> to get started.</div>
    @else
        <div class="content-grid" id="content-grid">
            @foreach($myContent as $item)
            <div class="content-card" id="content-{{ $item->id }}">
                <div class="content-card-thumb">🎬</div>
                <div class="content-card-body">
                    <div class="content-card-title" title="{{ $item->title }}">{{ $item->title }}</div>
                    <div class="content-card-meta">{{ ucfirst($item->type) }}{{ $item->year ? ' · ' . $item->year : '' }}</div>
                    <button class="content-card-del" onclick="deleteContent({{ $item->id }}, this)">✕ Remove</button>
                </div>
            </div>
            @endforeach
        </div>
    @endif
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

function toggleUpload() {
    var panel = document.getElementById('upload-panel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

async function uploadContent() {
    var alertEl = document.getElementById('upload-alert');
    alertEl.style.display = 'none';

    var title = document.getElementById('uc-title').value.trim();
    var url   = document.getElementById('uc-url').value.trim();
    if (!title || !url) {
        alertEl.className = 'alert alert-error';
        alertEl.textContent = 'Title and video URL are required.';
        alertEl.style.display = 'block';
        return;
    }

    var payload = {
        title:       title,
        type:        document.getElementById('uc-type').value,
        year:        document.getElementById('uc-year').value || null,
        description: document.getElementById('uc-desc').value.trim() || null,
        video_url:   url,
    };

    try {
        var xsrf = await getXsrfToken();
        var res = await fetch('/api/v1/creator/content', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': xsrf },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        });
        var data = await res.json();
        if (res.ok) {
            var t = data.title;
            var grid = document.getElementById('content-grid');
            if (!grid) {
                document.querySelector('.content-section .dash-empty').outerHTML =
                    '<div class="content-grid" id="content-grid"></div>';
                grid = document.getElementById('content-grid');
            }
            var card = document.createElement('div');
            card.className = 'content-card'; card.id = 'content-' + t.id;
            card.innerHTML = '<div class="content-card-thumb">🎬</div>' +
                '<div class="content-card-body">' +
                '<div class="content-card-title">' + escHtml(t.title) + '</div>' +
                '<div class="content-card-meta">' + ucFirst(t.type) + (t.year ? ' · ' + t.year : '') + '</div>' +
                '<button class="content-card-del" onclick="deleteContent(' + t.id + ', this)">✕ Remove</button>' +
                '</div>';
            grid.prepend(card);
            document.getElementById('uc-title').value = '';
            document.getElementById('uc-url').value = '';
            document.getElementById('uc-desc').value = '';
            document.getElementById('uc-year').value = '';
            document.getElementById('upload-panel').style.display = 'none';
        } else {
            var msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Upload failed.');
            alertEl.className = 'alert alert-error'; alertEl.textContent = msg; alertEl.style.display = 'block';
        }
    } catch(e) {
        alertEl.className = 'alert alert-error'; alertEl.textContent = 'Network error.'; alertEl.style.display = 'block';
    }
}

async function deleteContent(id, btn) {
    if (!confirm('Remove this content?')) return;
    try {
        var xsrf = await getXsrfToken();
        var res = await fetch('/api/v1/creator/content/' + id, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': xsrf },
            credentials: 'same-origin',
        });
        if (res.ok) {
            var card = document.getElementById('content-' + id);
            if (card) card.remove();
        }
    } catch(e) {}
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function ucFirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
</script>
@endsection

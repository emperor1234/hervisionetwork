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
    .upload-panel input[type=text], .upload-panel input[type=url],
    .upload-panel input[type=number], .upload-panel select, .upload-panel textarea {
        width: 100%; background: #1e1e1e; border: 1px solid #333;
        border-radius: 6px; color: #e0e0e0; padding: 9px 12px;
        font-size: 13px; font-family: inherit; outline: none;
        margin-bottom: 10px; transition: border-color .2s;
    }
    .upload-panel input[type=text]:focus, .upload-panel input[type=url]:focus,
    .upload-panel input[type=number]:focus, .upload-panel select:focus,
    .upload-panel textarea:focus { border-color: #F65F54; }
    .upload-panel select option { background: #1e1e1e; }
    .upload-panel textarea { resize: vertical; min-height: 70px; }
    .file-drop {
        border: 2px dashed #333; border-radius: 8px; padding: 20px; text-align: center;
        color: #555; font-size: 13px; cursor: pointer; margin-bottom: 10px;
        transition: border-color .2s;
    }
    .file-drop:hover, .file-drop.has-file { border-color: #F65F54; color: #bbb; }
    .file-drop input[type=file] { display: none; }
    .src-tabs { display: flex; gap: 0; margin-bottom: 12px; border-radius: 6px; overflow: hidden; border: 1px solid #333; }
    .src-tab {
        flex: 1; padding: 8px; text-align: center; font-size: 12px; color: #777;
        cursor: pointer; background: #1a1a1a; border: none; font-family: inherit;
        transition: background .15s, color .15s;
    }
    .src-tab.active { background: #F65F54; color: #fff; }
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

        <input type="text" id="uc-title" placeholder="Content title *">
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

        {{-- Cover image --}}
        <div style="font-size:12px;color:#888;margin-bottom:6px;">Cover Image <span style="color:#F65F54;">*</span></div>
        <div class="file-drop" id="cover-drop" onclick="document.getElementById('uc-cover').click()">
            <input type="file" id="uc-cover" accept="image/jpeg,image/png,image/webp" onchange="setFileLabel('cover-drop','uc-cover','cover image')">
            <span id="cover-label">Click to choose a cover image (JPG / PNG / WebP, max 5 MB)</span>
        </div>

        {{-- Video source toggle --}}
        <div style="font-size:12px;color:#888;margin-bottom:6px;">Video Source <span style="color:#F65F54;">*</span></div>
        <div class="src-tabs">
            <button class="src-tab active" id="tab-url" onclick="switchTab('url')">🔗 Link (YouTube / Vimeo / URL)</button>
            <button class="src-tab" id="tab-file" onclick="switchTab('file')">💾 Upload from computer</button>
        </div>
        <div id="src-url">
            <input type="url" id="uc-url" placeholder="https://youtube.com/watch?v=… or direct .mp4 URL">
        </div>
        <div id="src-file" style="display:none;">
            <div class="file-drop" id="video-drop" onclick="document.getElementById('uc-file').click()">
                <input type="file" id="uc-file" accept="video/mp4,video/webm,video/ogg,video/quicktime" onchange="setFileLabel('video-drop','uc-file','video')">
                <span id="video-label">Click to choose a video file (MP4 / WebM, max 500 MB)</span>
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:4px;">
            <button class="dash-edit-btn" style="width:auto;padding:9px 20px;" id="upload-btn" onclick="uploadContent()">Upload</button>
            <button class="dash-edit-btn" style="width:auto;padding:9px 16px;background:#333;" onclick="toggleUpload()">Cancel</button>
        </div>
    </div>

    @if($myContent->isEmpty())
        <div class="dash-empty">You haven't uploaded any content yet. Click <strong>+ Upload New</strong> to get started.</div>
    @else
        <div class="content-grid" id="content-grid">
            @foreach($myContent as $item)
            <div class="content-card" id="content-{{ $item->id }}">
                <div class="content-card-thumb">
                    @if($item->poster)
                        <img src="{{ $item->poster }}" alt="{{ $item->name }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        🎬
                    @endif
                </div>
                <div class="content-card-body">
                    <div class="content-card-title" title="{{ $item->name }}">{{ $item->name }}</div>
                    <div class="content-card-meta">{{ ucfirst($item->type) }}{{ $item->year ? ' · ' . $item->year : '' }}</div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:4px;">
                        <a href="/titles/{{ $item->id }}/{{ \Illuminate\Support\Str::slug($item->name) }}" style="font-size:11px;color:#F65F54;text-decoration:none;" target="_blank">View →</a>
                        <button class="content-card-del" onclick="deleteContent({{ $item->id }}, this)">✕ Remove</button>
                    </div>
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
        const xsrf = await getXsrfToken();
        const res = await fetch('/api/v1/creator/profile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': xsrf,
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

var _videoTab = 'url';

function switchTab(tab) {
    _videoTab = tab;
    document.getElementById('src-url').style.display  = tab === 'url'  ? 'block' : 'none';
    document.getElementById('src-file').style.display = tab === 'file' ? 'block' : 'none';
    document.getElementById('tab-url').classList.toggle('active',  tab === 'url');
    document.getElementById('tab-file').classList.toggle('active', tab === 'file');
}

function setFileLabel(dropId, inputId, label) {
    var f = document.getElementById(inputId).files[0];
    var drop = document.getElementById(dropId);
    if (f) {
        document.getElementById(dropId.replace('drop', 'label') || dropId + '-lbl') &&
            (document.getElementById(dropId.replace('drop', 'label') || dropId + '-lbl').textContent = f.name);
        drop.classList.add('has-file');
        // update the span inside
        drop.querySelector('span') && (drop.querySelector('span').textContent = f.name);
    }
}

function toggleUpload() {
    var panel = document.getElementById('upload-panel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

async function uploadContent() {
    var alertEl = document.getElementById('upload-alert');
    var btn     = document.getElementById('upload-btn');
    alertEl.style.display = 'none';

    var title     = document.getElementById('uc-title').value.trim();
    var coverFile = document.getElementById('uc-cover').files[0];

    if (!title) {
        alertEl.className = 'alert alert-error';
        alertEl.textContent = 'Please enter a title.';
        alertEl.style.display = 'block'; return;
    }
    if (!coverFile) {
        alertEl.className = 'alert alert-error';
        alertEl.textContent = 'Please choose a cover image.';
        alertEl.style.display = 'block'; return;
    }
    if (_videoTab === 'url' && !document.getElementById('uc-url').value.trim()) {
        alertEl.className = 'alert alert-error';
        alertEl.textContent = 'Please enter a video URL.';
        alertEl.style.display = 'block'; return;
    }
    if (_videoTab === 'file' && !document.getElementById('uc-file').files[0]) {
        alertEl.className = 'alert alert-error';
        alertEl.textContent = 'Please select a video file.';
        alertEl.style.display = 'block'; return;
    }

    var fd = new FormData();
    fd.append('title',       title);
    fd.append('type',        document.getElementById('uc-type').value);
    fd.append('cover',       coverFile);
    var year = document.getElementById('uc-year').value;
    if (year) fd.append('year', year);
    var desc = document.getElementById('uc-desc').value.trim();
    if (desc) fd.append('description', desc);
    if (_videoTab === 'url') {
        fd.append('video_url', document.getElementById('uc-url').value.trim());
    } else {
        fd.append('video_file', document.getElementById('uc-file').files[0]);
    }

    btn.disabled = true; btn.textContent = 'Uploading…';
    try {
        var xsrf = await getXsrfToken();
        var res = await fetch('/api/v1/creator/content', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': xsrf },
            credentials: 'same-origin',
            body: fd,
        });
        var data = await res.json();
        if (res.ok) {
            var t = data.title;
            var grid = document.getElementById('content-grid');
            if (!grid) {
                var empty = document.querySelector('.content-section .dash-empty');
                if (empty) empty.outerHTML = '<div class="content-grid" id="content-grid"></div>';
                grid = document.getElementById('content-grid');
            }
            var thumb = t.poster
                ? '<img src="' + escHtml(t.poster) + '" style="width:100%;height:100%;object-fit:cover;">'
                : '🎬';
            var card = document.createElement('div');
            card.className = 'content-card'; card.id = 'content-' + t.id;
            var slug = t.name.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
            card.innerHTML = '<div class="content-card-thumb">' + thumb + '</div>' +
                '<div class="content-card-body">' +
                '<div class="content-card-title">' + escHtml(t.name) + '</div>' +
                '<div class="content-card-meta">' + ucFirst(t.type) + (t.year ? ' · ' + t.year : '') + '</div>' +
                '<div style="display:flex;align-items:center;justify-content:space-between;margin-top:4px;">' +
                '<a href="/titles/' + t.id + '/' + slug + '" style="font-size:11px;color:#F65F54;text-decoration:none;" target="_blank">View →</a>' +
                '<button class="content-card-del" onclick="deleteContent(' + t.id + ', this)">✕ Remove</button>' +
                '</div>' +
                '</div>';
            grid.prepend(card);
            // reset form
            ['uc-title','uc-url','uc-desc','uc-year'].forEach(function(id){ var el=document.getElementById(id); if(el) el.value=''; });
            document.getElementById('uc-cover').value = '';
            document.getElementById('uc-file').value  = '';
            document.getElementById('cover-drop').classList.remove('has-file');
            document.getElementById('cover-drop').querySelector('span').textContent = 'Click to choose a cover image (JPG / PNG / WebP, max 5 MB)';
            document.getElementById('video-drop').classList.remove('has-file');
            document.getElementById('video-drop').querySelector('span').textContent = 'Click to choose a video file (MP4 / WebM, max 500 MB)';
            document.getElementById('upload-panel').style.display = 'none';
        } else {
            var msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Upload failed.');
            alertEl.className = 'alert alert-error'; alertEl.textContent = msg; alertEl.style.display = 'block';
        }
    } catch(e) {
        alertEl.className = 'alert alert-error'; alertEl.textContent = 'Network error. Please try again.'; alertEl.style.display = 'block';
    }
    btn.disabled = false; btn.textContent = 'Upload';
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

@extends('hvn.layout')
@section('title', 'Community — Her Vision Network')

@section('head')
<style>
/* ── Top bar ── */
.forum-topbar {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px; margin-bottom: 24px;
}
.forum-topbar h1 { font-size: 22px; font-weight: 600; color: #fff; }
.forum-topbar p  { font-size: 13px; color: #666; margin-top: 2px; }

/* ── Post cards ── */
.post-card-list { display: flex; flex-direction: column; gap: 10px; }

.post-card-link {
    display: block; text-decoration: none; color: inherit;
    background: #2a2a2a; border: 1px solid rgba(255,255,255,0.06);
    border-radius: 10px; padding: 16px 18px;
    transition: border-color .2s, background .2s;
}
.post-card-link:hover { border-color: #F65F54; background: #2d1a19; }

.post-card-body { display: flex; gap: 14px; align-items: flex-start; }

.post-avatar {
    flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, #a83428 0%, #F65F54 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 700; color: #fff; text-transform: uppercase;
}

.post-main { flex: 1; min-width: 0; }

.post-title {
    font-size: 15px; font-weight: 600; color: #eee;
    margin-bottom: 5px; line-height: 1.4;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.post-excerpt {
    font-size: 13px; color: #777; line-height: 1.55;
    display: -webkit-box; -webkit-line-clamp: 2;
    -webkit-box-orient: vertical; overflow: hidden;
    margin-bottom: 10px;
}

.post-meta-row {
    display: flex; align-items: center; flex-wrap: wrap; gap: 12px;
}
.post-author { font-size: 12px; color: #555; }
.post-author strong { color: #888; font-weight: 500; }

.post-badges { display: flex; gap: 8px; margin-left: auto; }
.badge {
    display: inline-flex; align-items: center; gap: 4px;
    background: #222; border: 1px solid #2e2e2e;
    border-radius: 20px; padding: 3px 10px;
    font-size: 12px; color: #888;
}
.badge svg { width: 13px; height: 13px; fill: none; stroke: currentColor; stroke-width: 2; }

/* ── Compose ── */
.compose-bar {
    display: flex; align-items: center; gap: 12px;
    background: #2a2a2a; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px;
    padding: 12px 16px; margin-bottom: 20px; cursor: pointer;
    transition: border-color .2s;
}
.compose-bar:hover { border-color: #F65F54; }
.compose-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: #2a2a2a; display: flex; align-items: center; justify-content: center;
    font-size: 14px; color: #666; flex-shrink: 0;
}
.compose-placeholder { flex: 1; font-size: 14px; color: #555; }

/* ── New post panel ── */
.new-post-panel { margin-bottom: 20px; }
.new-post-panel h3 { font-size: 15px; font-weight: 500; color: #bbb; margin-bottom: 14px; }
.np-input {
    width: 100%; background: #111; border: 1px solid #333; border-radius: 6px;
    color: #e0e0e0; padding: 11px 14px; font-size: 15px; font-family: inherit;
    outline: none; margin-bottom: 10px; transition: border-color .2s;
}
.np-input:focus { border-color: #F65F54; }
.np-textarea {
    width: 100%; background: #111; border: 1px solid #333; border-radius: 6px;
    color: #e0e0e0; padding: 12px 14px; font-size: 14px; font-family: inherit;
    resize: vertical; min-height: 90px; outline: none; transition: border-color .2s;
}
.np-textarea:focus { border-color: #F65F54; }
.np-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; }
.np-cancel { background: none; border: none; color: #666; font-size: 13px; cursor: pointer; font-family: inherit; }
.np-cancel:hover { color: #aaa; }

/* ── Empty ── */
.forum-empty { text-align: center; padding: 56px 20px; background: #2a2a2a; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; }
.forum-empty h3 { font-size: 18px; color: #666; margin-bottom: 8px; }
.forum-empty p { font-size: 14px; color: #555; }

/* ── Pagination ── */
.forum-pagination { display: flex; gap: 8px; align-items: center; justify-content: center; margin-top: 20px; }
.forum-pagination a, .forum-pagination span {
    padding: 7px 16px; border-radius: 6px; font-size: 13px;
    background: #2a2a2a; border: 1px solid rgba(255,255,255,0.06); color: #aaa; text-decoration: none;
}
.forum-pagination a:hover { background: #F65F54; border-color: #F65F54; color: #fff; }
.forum-pagination .pg-info { background: transparent; border-color: transparent; color: #555; }
</style>
@endsection

@section('content')

<div class="forum-topbar">
    <div>
        <h1>Community Forum</h1>
        <p>Ask questions, share ideas and connect with the Her Vision Network community.</p>
    </div>
    @guest
        <a href="/login" class="btn-sm">Sign in to post</a>
    @endguest
</div>

{{-- Compose bar (logged-in) --}}
@auth
<div class="compose-bar" onclick="openCompose()" id="compose-bar">
    <div class="compose-avatar">✏️</div>
    <div class="compose-placeholder">Start a new discussion…</div>
    <button class="btn-sm" style="pointer-events:none;">+ New Post</button>
</div>

<div class="hvn-card new-post-panel" id="new-post-panel" style="display:none;">
    <h3>Start a Discussion</h3>
    <div id="write-alert" class="alert" style="display:none; margin-bottom:10px;"></div>
    <input class="np-input" type="text" id="post-title" placeholder="Give your discussion a clear title…">
    <textarea class="np-textarea" id="post-body" placeholder="Share your thoughts, question or idea. Be specific — good discussions start with detail!"></textarea>
    <div class="np-actions">
        <button class="np-cancel" onclick="closeCompose()">Cancel</button>
        <button class="btn-sm" id="post-btn" onclick="submitPost()">Post Discussion</button>
    </div>
</div>
@endauth

{{-- Post list --}}
@if($posts->total() === 0)
<div class="forum-empty">
    <h3>No discussions yet</h3>
    <p>Be the first to start a conversation in the community!</p>
</div>
@else
<div class="post-card-list">
    @foreach($posts as $post)
    <a href="/community/{{ $post->id }}/{{ Str::slug($post->title) }}" class="post-card-link">
        <div class="post-card-body">
            <div class="post-avatar">{{ strtoupper(substr($post->user->username ?? '?', 0, 1)) }}</div>
            <div class="post-main">
                <div class="post-title">{{ $post->title }}</div>
                <div class="post-excerpt">{{ $post->body }}</div>
                <div class="post-meta-row">
                    <span class="post-author">by <strong>{{ $post->user->username ?? 'Unknown' }}</strong> · {{ $post->created_at->diffForHumans() }}</span>
                    <div class="post-badges">
                        <span class="badge">
                            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            {{ $post->comments_count }}
                        </span>
                        <span class="badge">
                            <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                            {{ $post->likes_count }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @endforeach
</div>

@if($posts->hasPages())
<div class="forum-pagination">
    @if($posts->onFirstPage())
        <span style="opacity:.35;">← Prev</span>
    @else
        <a href="{{ $posts->previousPageUrl() }}">← Prev</a>
    @endif
    <span class="pg-info">Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</span>
    @if($posts->hasMorePages())
        <a href="{{ $posts->nextPageUrl() }}">Next →</a>
    @else
        <span style="opacity:.35;">Next →</span>
    @endif
</div>
@endif
@endif

@endsection

@section('scripts')
<script>
function openCompose() {
    document.getElementById('compose-bar').style.display = 'none';
    document.getElementById('new-post-panel').style.display = 'block';
    document.getElementById('post-title').focus();
}
function closeCompose() {
    document.getElementById('new-post-panel').style.display = 'none';
    document.getElementById('compose-bar').style.display = 'flex';
}

async function submitPost() {
    const title   = document.getElementById('post-title').value.trim();
    const body    = document.getElementById('post-body').value.trim();
    const alertEl = document.getElementById('write-alert');
    const btn     = document.getElementById('post-btn');

    alertEl.style.display = 'none';
    if (!title || !body) {
        alertEl.className = 'alert alert-error';
        alertEl.textContent = 'Please fill in both the title and the body.';
        alertEl.style.display = 'block';
        return;
    }
    btn.disabled = true; btn.textContent = 'Posting…';

    try {
        const xsrf = await getXsrfToken();
        const res = await fetch('/api/v1/community/posts', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': xsrf },
            credentials: 'same-origin',
            body: JSON.stringify({ title, body })
        });
        if (res.ok) {
            window.location.reload();
        } else {
            const data = await res.json();
            const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Could not post.');
            alertEl.className = 'alert alert-error';
            alertEl.textContent = msg;
            alertEl.style.display = 'block';
            btn.disabled = false; btn.textContent = 'Post Discussion';
        }
    } catch {
        alertEl.className = 'alert alert-error';
        alertEl.textContent = 'Network error. Please try again.';
        alertEl.style.display = 'block';
        btn.disabled = false; btn.textContent = 'Post Discussion';
    }
}
</script>
@endsection

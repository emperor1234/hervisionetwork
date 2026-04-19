@extends('hvn.layout')
@section('title', 'Community — Her Vision Network')

@section('head')
<style>
    /* ── Hero ── */
    .forum-hero { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 28px; }
    .forum-hero h1 { font-size: 26px; font-weight: 600; color: #fff; margin-bottom: 4px; }
    .forum-hero p  { color: #888; font-size: 14px; }

    /* ── New Post Panel ── */
    .new-post-panel h3 { font-size: 15px; font-weight: 500; color: #bbb; margin-bottom: 14px; }
    .new-post-panel input[type=text] {
        width: 100%; background: #111; border: 1px solid #333; border-radius: 6px;
        color: #e0e0e0; padding: 11px 14px; font-size: 15px; font-family: inherit;
        outline: none; margin-bottom: 10px; transition: border-color .2s;
    }
    .new-post-panel input[type=text]:focus { border-color: #6c63ff; }
    .new-post-panel textarea {
        width: 100%; background: #111; border: 1px solid #333; border-radius: 6px;
        color: #e0e0e0; padding: 12px 14px; font-size: 14px; font-family: inherit;
        resize: vertical; min-height: 90px; outline: none; transition: border-color .2s;
        margin-bottom: 0;
    }
    .new-post-panel textarea:focus { border-color: #6c63ff; }
    .new-post-actions { display: flex; justify-content: flex-end; margin-top: 10px; }

    /* ── Thread List ── */
    .thread-list { display: flex; flex-direction: column; gap: 1px; background: #1e1e1e; border: 1px solid #2a2a2a; border-radius: 10px; overflow: hidden; }

    .thread-header {
        display: grid; grid-template-columns: 1fr 72px 72px 100px;
        background: #141414; padding: 10px 18px;
        font-size: 11px; color: #444; text-transform: uppercase; letter-spacing: .08em;
        border-bottom: 1px solid #2a2a2a;
    }
    .thread-header span:not(:first-child) { text-align: center; }
    .thread-header span:last-child { text-align: right; }

    .thread-row {
        display: grid; grid-template-columns: 1fr 72px 72px 100px;
        background: #161616; padding: 14px 18px;
        text-decoration: none; color: inherit;
        align-items: center; transition: background .15s;
        border-bottom: 1px solid #1e1e1e;
    }
    .thread-row:last-child { border-bottom: none; }
    .thread-row:hover { background: #1c1c2e; }

    .thread-info { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .thread-avatar {
        width: 36px; height: 36px; flex-shrink: 0;
        border-radius: 50%; background: linear-gradient(135deg,#3d3580,#6c63ff);
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 600; color: #fff; text-transform: uppercase;
    }
    .thread-text { min-width: 0; }
    .thread-text strong { display: block; font-size: 14px; font-weight: 500; color: #e8e8e8; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .thread-text small  { font-size: 12px; color: #555; }

    .thread-stat { text-align: center; font-size: 13px; color: #666; }
    .thread-stat span { display: block; font-size: 10px; color: #3a3a3a; text-transform: uppercase; letter-spacing: .05em; margin-top: 1px; }

    .thread-time { text-align: right; font-size: 12px; color: #555; white-space: nowrap; }

    /* ── Empty ── */
    .forum-empty { text-align: center; padding: 56px 20px; color: #444; }
    .forum-empty h3 { font-size: 18px; color: #666; margin-bottom: 8px; }
    .forum-empty p { font-size: 14px; }

    /* ── Pagination ── */
    .forum-pagination { display: flex; gap: 8px; align-items: center; justify-content: center; margin-top: 20px; }
    .forum-pagination a, .forum-pagination span {
        padding: 7px 14px; border-radius: 6px; font-size: 13px;
        background: #1a1a1a; border: 1px solid #2a2a2a; color: #aaa; text-decoration: none;
    }
    .forum-pagination a:hover { background: #6c63ff; border-color: #6c63ff; color: #fff; }
    .forum-pagination .page-info { background: transparent; border-color: transparent; color: #555; }

    @media (max-width: 560px) {
        .thread-header,
        .thread-row { grid-template-columns: 1fr 56px 90px; }
        .thread-header span:nth-child(3),
        .thread-row .thread-stat:nth-child(3) { display: none; }
    }
</style>
@endsection

@section('content')
<div class="forum-hero">
    <div>
        <h1>Community Forum</h1>
        <p>Share ideas, ask questions, connect with creators and viewers.</p>
    </div>
    @auth
        <a href="#new-post" class="btn-sm" onclick="document.getElementById('new-post').scrollIntoView({behavior:'smooth'});return false;">+ New Discussion</a>
    @else
        <a href="/login" class="btn-sm">Sign in to post</a>
    @endauth
</div>

@auth
<div class="hvn-card new-post-panel" id="new-post" style="margin-bottom:24px;">
    <h3>Start a Discussion</h3>
    <div id="write-alert" class="alert" style="display:none; margin-bottom:10px;"></div>
    <input type="text" id="post-title" placeholder="Give your discussion a title…">
    <textarea id="post-body" placeholder="What's on your mind? Share details, ask questions, start a conversation…"></textarea>
    <div class="new-post-actions">
        <button class="btn-sm" id="post-btn" onclick="submitPost()">Post Discussion</button>
    </div>
</div>
@endauth

@if($posts->total() === 0)
    <div class="thread-list">
        <div class="forum-empty">
            <h3>No discussions yet</h3>
            <p>Be the first to start a conversation in the community!</p>
        </div>
    </div>
@else
<div class="thread-list">
    <div class="thread-header">
        <span>Discussion</span>
        <span>Replies</span>
        <span>Likes</span>
        <span style="text-align:right;">Activity</span>
    </div>
    @foreach($posts as $post)
    <a href="/community/{{ $post->id }}" class="thread-row">
        <div class="thread-info">
            <div class="thread-avatar">{{ strtoupper(substr($post->user->username ?? '?', 0, 1)) }}</div>
            <div class="thread-text">
                <strong>{{ $post->title }}</strong>
                <small>by {{ $post->user->username ?? 'Unknown' }}</small>
            </div>
        </div>
        <div class="thread-stat">{{ $post->comments_count }}<span>replies</span></div>
        <div class="thread-stat">{{ $post->likes_count }}<span>likes</span></div>
        <div class="thread-time">{{ $post->created_at->diffForHumans() }}</div>
    </a>
    @endforeach
</div>

@if($posts->hasPages())
<div class="forum-pagination">
    @if($posts->onFirstPage())
        <span style="opacity:.4;">← Prev</span>
    @else
        <a href="{{ $posts->previousPageUrl() }}">← Prev</a>
    @endif
    <span class="page-info">Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</span>
    @if($posts->hasMorePages())
        <a href="{{ $posts->nextPageUrl() }}">Next →</a>
    @else
        <span style="opacity:.4;">Next →</span>
    @endif
</div>
@endif
@endif
@endsection

@section('scripts')
<script>
async function submitPost() {
    const title   = document.getElementById('post-title').value.trim();
    const body    = document.getElementById('post-body').value.trim();
    const alertEl = document.getElementById('write-alert');
    const btn     = document.getElementById('post-btn');

    alertEl.style.display = 'none';
    if (!title || !body) {
        alertEl.className = 'alert alert-error';
        alertEl.textContent = 'Please fill in both the title and body.';
        alertEl.style.display = 'block';
        return;
    }
    btn.disabled = true; btn.textContent = 'Posting…';

    try {
        const res = await fetch('/community/posts', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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

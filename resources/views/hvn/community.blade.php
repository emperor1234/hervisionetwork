@extends('hvn.layout')
@section('title', 'Community Forum — Her Vision Network')

@section('head')
<style>
    .forum-hero { margin-bottom: 28px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .forum-hero h1 { font-size: 26px; font-weight: 600; color: #fff; margin-bottom: 4px; }
    .forum-hero p  { color: #888; font-size: 14px; }

    .new-post-panel { margin-bottom: 24px; }
    .new-post-panel h3 { font-size: 15px; font-weight: 500; color: #ccc; margin-bottom: 14px; }
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
    }
    .new-post-panel textarea:focus { border-color: #6c63ff; }
    .post-actions { display: flex; justify-content: flex-end; margin-top: 10px; }

    .forum-table { border: 1px solid #2a2a2a; border-radius: 8px; overflow: hidden; }
    .forum-table-head {
        display: grid; grid-template-columns: 1fr 64px 64px 110px;
        background: #141414; padding: 10px 20px;
        font-size: 12px; color: #555; text-transform: uppercase; letter-spacing: .06em;
        border-bottom: 1px solid #2a2a2a;
    }
    .forum-row {
        display: grid; grid-template-columns: 1fr 64px 64px 110px;
        padding: 16px 20px; border-bottom: 1px solid #1e1e1e;
        text-decoration: none; color: inherit; transition: background .15s;
        align-items: center;
    }
    .forum-row:last-child { border-bottom: none; }
    .forum-row:hover { background: #1a1a1a; }
    .forum-row-title strong { display: block; font-size: 15px; color: #e8e8e8; margin-bottom: 3px; line-height: 1.4; }
    .forum-row-title small  { font-size: 12px; color: #555; }
    .forum-row-stat { text-align: center; font-size: 14px; color: #666; }
    .forum-row-time { font-size: 12px; color: #555; text-align: right; }

    @media (max-width: 540px) {
        .forum-table-head { grid-template-columns: 1fr 48px 80px; }
        .forum-table-head .col-likes,
        .forum-row .forum-row-stat:nth-child(3) { display: none; }
        .forum-row { grid-template-columns: 1fr 48px 80px; }
    }
</style>
@endsection

@section('content')
<div class="forum-hero">
    <div>
        <h1>Community Forum</h1>
        <p>Share your thoughts, connect with creators and fellow viewers.</p>
    </div>
    @auth
        <a href="#new-post" class="btn-sm" onclick="document.getElementById('new-post').scrollIntoView({behavior:'smooth'});return false;">+ New Post</a>
    @else
        <a href="/login" class="btn-sm">Sign in to post</a>
    @endauth
</div>

@auth
<div class="hvn-card new-post-panel" id="new-post">
    <h3>Start a Discussion</h3>
    <div id="write-alert" class="alert" style="display:none; margin-bottom:10px;"></div>
    <input type="text" id="post-title" placeholder="Title of your post…">
    <textarea id="post-body" placeholder="What's on your mind?"></textarea>
    <div class="post-actions">
        <button class="btn-sm" id="post-btn" onclick="submitPost()">Post</button>
    </div>
</div>
@endauth

@if($posts->total() === 0)
    <div class="empty-state">
        <h3>No discussions yet</h3>
        <p>Be the first to start a conversation!</p>
    </div>
@else
<div class="forum-table">
    <div class="forum-table-head">
        <span>Discussion</span>
        <span style="text-align:center;">Replies</span>
        <span class="col-likes" style="text-align:center;">Likes</span>
        <span style="text-align:right;">Posted</span>
    </div>
    @foreach($posts as $post)
    <a href="/community/{{ $post->id }}" class="forum-row">
        <div class="forum-row-title">
            <strong>{{ $post->title }}</strong>
            <small>by {{ $post->user->username ?? 'Unknown' }}</small>
        </div>
        <div class="forum-row-stat">{{ $post->comments_count }}</div>
        <div class="forum-row-stat col-likes">{{ $post->likes_count }}</div>
        <div class="forum-row-time">{{ $post->created_at->diffForHumans() }}</div>
    </a>
    @endforeach
</div>

@if($posts->hasPages())
<div class="pagination" style="margin-top:20px;">
    @if($posts->onFirstPage())
        <span style="opacity:.4; padding:8px 14px; font-size:14px;">← Prev</span>
    @else
        <a href="{{ $posts->previousPageUrl() }}">← Prev</a>
    @endif
    <span style="padding:8px 14px; font-size:14px; color:#888;">Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</span>
    @if($posts->hasMorePages())
        <a href="{{ $posts->nextPageUrl() }}">Next →</a>
    @else
        <span style="opacity:.4; padding:8px 14px; font-size:14px;">Next →</span>
    @endif
</div>
@endif
@endif
@endsection

@section('scripts')
<script>
async function submitPost() {
    const title = document.getElementById('post-title').value.trim();
    const body  = document.getElementById('post-body').value.trim();
    const alertEl = document.getElementById('write-alert');
    const btn   = document.getElementById('post-btn');

    alertEl.style.display = 'none';
    if (!title || !body) {
        alertEl.className = 'alert alert-error';
        alertEl.textContent = 'Please fill in both the title and the body.';
        alertEl.style.display = 'block';
        return;
    }
    btn.disabled = true;
    btn.textContent = 'Posting…';

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
            btn.disabled = false;
            btn.textContent = 'Post';
        }
    } catch {
        alertEl.className = 'alert alert-error';
        alertEl.textContent = 'Network error. Please try again.';
        alertEl.style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'Post';
    }
}
</script>
@endsection

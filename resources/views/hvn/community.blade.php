@extends('hvn.layout')
@section('title', 'Community — Her Vision Network')

@section('content')
<div class="page-heading">
    <h1>Community</h1>
    <p>Share your thoughts, connect with creators and fellow viewers.</p>
</div>

@auth
<div class="hvn-card write-post" style="margin-bottom:24px;">
    <div id="write-alert" class="alert" style="display:none; margin-bottom:10px;"></div>
    <input type="text" id="post-title" placeholder="Post title…">
    <textarea id="post-body" placeholder="What's on your mind?"></textarea>
    <div style="margin-top:10px; text-align:right;">
        <button class="btn-sm" id="post-btn" onclick="submitPost()">Post</button>
    </div>
</div>
@endauth

<div id="posts-container">
@forelse($posts as $post)
    <div class="hvn-card post-card">
        <h3>{{ $post->title }}</h3>
        <div class="meta">
            <span>{{ $post->user->username ?? 'Unknown' }}</span>
            <span>{{ $post->created_at->diffForHumans() }}</span>
        </div>
        <p class="body-preview">{{ Str::limit($post->body, 200) }}</p>
        <div class="stats">
            <span>💬 {{ $post->comments_count }} comment{{ $post->comments_count !== 1 ? 's' : '' }}</span>
            <span>❤️ {{ $post->likes_count }} like{{ $post->likes_count !== 1 ? 's' : '' }}</span>
        </div>
    </div>
@empty
    <div class="empty-state">
        <h3>No posts yet</h3>
        <p>Be the first to start a conversation.</p>
    </div>
@endforelse
</div>

@if($posts->hasPages())
<div class="pagination">
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
@endsection

@section('scripts')
<script>
async function submitPost() {
    const title = document.getElementById('post-title').value.trim();
    const body  = document.getElementById('post-body').value.trim();
    const alert = document.getElementById('write-alert');
    const btn   = document.getElementById('post-btn');

    alert.style.display = 'none';
    if (!title || !body) {
        alert.className = 'alert alert-error';
        alert.textContent = 'Please fill in both the title and the body.';
        alert.style.display = 'block';
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Posting…';

    try {
        const res = await fetch('/api/v1/community/posts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ title, body })
        });

        if (res.ok) {
            window.location.reload();
        } else {
            const data = await res.json();
            const msgs = data.errors
                ? Object.values(data.errors).flat().join(' ')
                : (data.message || 'Could not post. Please try again.');
            alert.className = 'alert alert-error';
            alert.textContent = msgs;
            alert.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Post';
        }
    } catch {
        alert.className = 'alert alert-error';
        alert.textContent = 'Network error. Please try again.';
        alert.style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'Post';
    }
}
</script>
@endsection

@extends('hvn.layout')
@section('title', e($post->title) . ' — Community — Her Vision Network')

@section('head')
<style>
    .post-header { margin-bottom: 28px; }
    .post-header a.back { font-size: 13px; color: #666; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 18px; }
    .post-header a.back:hover { color: #aaa; }
    .post-header h1 { font-size: 22px; font-weight: 600; color: #f0f0f0; line-height: 1.4; margin-bottom: 8px; }
    .post-meta { font-size: 12px; color: #555; }
    .post-meta span { margin-right: 14px; }

    .post-body { font-size: 15px; line-height: 1.7; color: #ccc; white-space: pre-wrap; word-break: break-word; }

    .post-stats { display: flex; gap: 20px; margin-top: 18px; padding-top: 16px; border-top: 1px solid #1e1e1e; }
    .post-stats span { font-size: 13px; color: #555; }

    .comments-section { margin-top: 32px; }
    .comments-section h2 { font-size: 16px; font-weight: 500; color: #bbb; margin-bottom: 18px; }

    .comment-item { display: flex; gap: 12px; margin-bottom: 20px; }
    .comment-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        background: #2a2a2a; border: 1px solid #333;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; color: #888; flex-shrink: 0; text-transform: uppercase;
    }
    .comment-bubble { flex: 1; }
    .comment-bubble .comment-meta { font-size: 12px; color: #555; margin-bottom: 5px; }
    .comment-bubble .comment-meta strong { color: #999; font-weight: 500; margin-right: 8px; }
    .comment-bubble p { font-size: 14px; color: #ccc; line-height: 1.6; white-space: pre-wrap; word-break: break-word; }

    .comment-form { margin-top: 28px; border-top: 1px solid #1e1e1e; padding-top: 24px; }
    .comment-form h3 { font-size: 14px; font-weight: 500; color: #888; margin-bottom: 12px; }
    .comment-form textarea {
        width: 100%; background: #111; border: 1px solid #333; border-radius: 6px;
        color: #e0e0e0; padding: 12px 14px; font-size: 14px; font-family: inherit;
        resize: vertical; min-height: 80px; outline: none; transition: border-color .2s;
    }
    .comment-form textarea:focus { border-color: #F65F54; }  /* matches layout accent */
    .comment-form-actions { display: flex; justify-content: flex-end; margin-top: 10px; }
    #comment-alert { margin-bottom: 10px; }

    .empty-comments { text-align: center; padding: 28px 0; color: #444; font-size: 14px; }
</style>
@endsection

@section('content')
<div class="post-header">
    <a href="/community" class="back">← Back to Community</a>
    <h1>{{ $post->title }}</h1>
    <div class="post-meta">
        <span>by <strong style="color:#888;">{{ $post->user->username ?? 'Unknown' }}</strong></span>
        <span>{{ $post->created_at->diffForHumans() }}</span>
    </div>
</div>

<div class="hvn-card" style="margin-bottom:24px;">
    <div class="post-body">{{ $post->body }}</div>
    <div class="post-stats">
        <span id="reply-count">{{ $post->comments_count }} {{ Str::plural('reply', $post->comments_count) }}</span>
        <button id="like-btn" onclick="toggleLike()" style="background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px;font-size:13px;color:#555;padding:0;font-family:inherit;" title="Like this post">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            <span id="like-count">{{ $post->likes_count }}</span> {{ Str::plural('like', $post->likes_count) }}
        </button>
    </div>
</div>

<div class="hvn-card comments-section">
    <h2 id="comments-heading">
        {{ $post->comments_count > 0 ? $post->comments_count . ' ' . Str::plural('Comment', $post->comments_count) : 'Comments' }}
    </h2>

    <div id="comments-list">
        @if($post->comments->isEmpty())
            <div class="empty-comments" id="no-comments">No comments yet. Be the first to reply!</div>
        @else
            @foreach($post->comments->sortBy('created_at') as $comment)
            <div class="comment-item">
                <div class="comment-avatar">{{ strtoupper(substr($comment->user->username ?? '?', 0, 1)) }}</div>
                <div class="comment-bubble">
                    <div class="comment-meta">
                        <strong>{{ $comment->user->username ?? 'Unknown' }}</strong>
                        {{ $comment->created_at->diffForHumans() }}
                    </div>
                    <p>{{ $comment->body }}</p>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    @auth
    <div class="comment-form">
        <h3>Leave a comment</h3>
        <div id="comment-alert" class="alert" style="display:none;"></div>
        <textarea id="comment-body" placeholder="Write your reply…"></textarea>
        <div class="comment-form-actions">
            <button class="btn-sm" id="comment-btn" onclick="submitComment()">Reply</button>
        </div>
    </div>
    @else
    <div style="margin-top:24px; padding-top:20px; border-top:1px solid #1e1e1e; text-align:center; font-size:14px; color:#555;">
        <a href="/login" style="color:#F65F54; text-decoration:none;">Sign in</a> to leave a comment.
    </div>
    @endauth
</div>
@endsection

@section('scripts')
<script>
async function submitComment() {
    const body    = document.getElementById('comment-body').value.trim();
    const alertEl = document.getElementById('comment-alert');
    const btn     = document.getElementById('comment-btn');

    alertEl.style.display = 'none';
    if (!body) {
        alertEl.className = 'alert alert-error';
        alertEl.textContent = 'Please write something before replying.';
        alertEl.style.display = 'block';
        return;
    }
    btn.disabled = true;
    btn.textContent = 'Posting…';

    try {
        const xsrf = await getXsrfToken();
        const res = await fetch('/api/v1/community/posts/{{ $post->id }}/comments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': xsrf,
            },
            credentials: 'same-origin',
            body: JSON.stringify({ body })
        });

        if (res.ok) {
            const data = await res.json();
            const c = data.comment;
            const username = (c.user && c.user.username) ? c.user.username : 'You';
            const initial  = username.charAt(0).toUpperCase();

            document.getElementById('no-comments') && document.getElementById('no-comments').remove();

            const el = document.createElement('div');
            el.className = 'comment-item';
            el.innerHTML = `
                <div class="comment-avatar">${initial}</div>
                <div class="comment-bubble">
                    <div class="comment-meta"><strong>${escHtml(username)}</strong> just now</div>
                    <p>${escHtml(c.body)}</p>
                </div>`;
            document.getElementById('comments-list').appendChild(el);

            document.getElementById('comment-body').value = '';
            btn.disabled = false;
            btn.textContent = 'Reply';

            // update counts
            const countEl = document.getElementById('reply-count');
            const headEl  = document.getElementById('comments-heading');
            const current = parseInt(countEl.textContent) || 0;
            const next    = current + 1;
            countEl.textContent = next + (next === 1 ? ' reply' : ' replies');
            headEl.textContent  = next + (next === 1 ? ' Comment' : ' Comments');
        } else {
            const data = await res.json();
            const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Could not post.');
            alertEl.className = 'alert alert-error';
            alertEl.textContent = msg;
            alertEl.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Reply';
        }
    } catch {
        alertEl.className = 'alert alert-error';
        alertEl.textContent = 'Network error. Please try again.';
        alertEl.style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'Reply';
    }
}

async function toggleLike() {
    const btn = document.getElementById('like-btn');
    btn.disabled = true;
    try {
        const xsrf = await getXsrfToken();
        const res = await fetch('/api/v1/community/posts/{{ $post->id }}/like', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': xsrf },
            credentials: 'same-origin',
        });
        if (res.ok) {
            const data = await res.json();
            const count = data.likes_count !== undefined ? data.likes_count : (parseInt(document.getElementById('like-count').textContent) + (data.liked ? 1 : -1));
            document.getElementById('like-count').textContent = count;
            btn.style.color = data.liked ? '#F65F54' : '#555';
            btn.querySelector('svg').style.fill = data.liked ? '#F65F54' : 'none';
        }
    } catch(e) {}
    btn.disabled = false;
}

function escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endsection

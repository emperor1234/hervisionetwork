@extends('hvn.admin.layout')
@section('title', 'Dashboard')

@section('content')
<div class="page-heading">
    <h1>Dashboard</h1>
    <p>Platform overview at a glance.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Creators</div>
        <div class="stat-value">{{ $stats['creators'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Viewers</div>
        <div class="stat-value">{{ $stats['viewers'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Community Posts</div>
        <div class="stat-value">{{ $stats['posts'] }}</div>
        <div class="stat-sub">{{ $stats['published'] }} published</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Creator Content</div>
        <div class="stat-value">{{ $stats['content'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">News Articles</div>
        <div class="stat-value">{{ $stats['news'] }}</div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">

    {{-- Recent Creators --}}
    <div>
        <div class="section-header">
            <h2>Recent Creators</h2>
            <a href="/hvn/admin/creators" style="font-size:13px;color:#F65F54;text-decoration:none;">View all →</a>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCreators as $creator)
                    <tr>
                        <td>
                            <a href="/creators/{{ $creator->username }}" style="color:#e0e0e0;text-decoration:none;">{{ $creator->username }}</a>
                            <div style="font-size:12px;color:#555;">{{ $creator->email }}</div>
                        </td>
                        <td style="color:#555;font-size:12px;white-space:nowrap;">{{ $creator->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" style="text-align:center;color:#444;padding:24px;">No creators yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Posts --}}
    <div>
        <div class="section-header">
            <h2>Recent Posts</h2>
            <a href="/hvn/admin/community" style="font-size:13px;color:#F65F54;text-decoration:none;">View all →</a>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPosts as $post)
                    <tr>
                        <td>
                            <a href="/community/{{ $post->id }}" style="color:#e0e0e0;text-decoration:none;display:block;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $post->title }}</a>
                            <div style="font-size:12px;color:#555;">by {{ $post->user->username ?? 'Unknown' }}</div>
                        </td>
                        <td>
                            @if($post->status === 'published')
                                <span class="badge badge-green">Published</span>
                            @elseif($post->status === 'removed')
                                <span class="badge badge-red">Hidden</span>
                            @else
                                <span class="badge badge-gray">{{ ucfirst($post->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="2" style="text-align:center;color:#444;padding:24px;">No posts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

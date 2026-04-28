@extends('hvn.admin.layout')
@section('title', 'Content')

@section('content')
<div class="page-heading">
    <h1>Creator Content</h1>
    <p>Review and manage videos uploaded by creators.</p>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Cover</th>
                <th>Videos</th>
                <th>Uploaded</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($titles as $title)
            <tr>
                <td>
                    <div style="font-weight:500;color:#e0e0e0;">{{ $title->name }}</div>
                    @if($title->description)
                        <div style="font-size:12px;color:#555;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:240px;">
                            {{ \Illuminate\Support\Str::limit($title->description, 80) }}
                        </div>
                    @endif
                </td>
                <td>
                    @if($title->poster)
                        <img src="{{ $title->poster }}" alt="{{ $title->name }}"
                             style="height:48px;width:34px;object-fit:cover;border-radius:3px;border:1px solid #333;">
                    @else
                        <span style="font-size:12px;color:#444;">None</span>
                    @endif
                </td>
                <td>
                    @foreach($title->videos as $video)
                        <div style="font-size:12px;color:#777;margin-bottom:2px;">
                            <span class="badge {{ $video->source === 'local' ? 'badge-amber' : 'badge-gray' }}" style="margin-right:4px;">{{ $video->source }}</span>
                            {{ \Illuminate\Support\Str::limit($video->name ?: $video->url, 50) }}
                        </div>
                    @endforeach
                    @if($title->videos->isEmpty())
                        <span style="font-size:12px;color:#444;">No videos</span>
                    @endif
                </td>
                <td style="color:#555;font-size:12px;white-space:nowrap;">{{ $title->created_at->diffForHumans() }}</td>
                <td>
                    <form method="POST" action="/hvn/admin/content/{{ $title->id }}" style="margin:0;"
                          onsubmit="return confirm('Delete this content and all associated videos? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;color:#444;padding:40px;">No creator content uploaded yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($titles->hasPages())
<div class="pagination">
    @if($titles->onFirstPage())
        <span style="opacity:.35;">← Prev</span>
    @else
        <a href="{{ $titles->previousPageUrl() }}">← Prev</a>
    @endif
    <span class="pg-info">Page {{ $titles->currentPage() }} of {{ $titles->lastPage() }}</span>
    @if($titles->hasMorePages())
        <a href="{{ $titles->nextPageUrl() }}">Next →</a>
    @else
        <span style="opacity:.35;">Next →</span>
    @endif
</div>
@endif
@endsection

@extends('hvn.layout')
@section('title', 'Creators — Her Vision Network')

@section('content')
<div class="page-heading">
    <h1>Creators</h1>
    <p>Discover indie content creators on Her Vision Network.</p>
</div>

@if($creators->isEmpty())
    <div class="empty-state">
        <h3>No creators yet</h3>
        <p>Be the first — <a href="/creator-signup" style="color:#6c63ff;">join as a Creator</a>.</p>
    </div>
@else
    <div class="creator-grid">
    @foreach($creators as $profile)
        <a href="/creators/{{ $profile->user_id }}" class="hvn-card creator-card">
            <div class="creator-avatar">
                @if($profile->profile_photo)
                    <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="{{ $profile->display_name }}">
                @else
                    {{ strtoupper(substr($profile->display_name ?: '?', 0, 1)) }}
                @endif
            </div>
            <div class="creator-name">{{ $profile->display_name }}</div>
            @if($profile->bio)
                <div class="creator-bio">{{ Str::limit($profile->bio, 100) }}</div>
            @endif
        </a>
    @endforeach
    </div>

    @if($creators->hasPages())
    <div class="pagination">
        @if($creators->onFirstPage())
            <span style="opacity:.4; padding:8px 14px; font-size:14px;">← Prev</span>
        @else
            <a href="{{ $creators->previousPageUrl() }}">← Prev</a>
        @endif
        <span style="padding:8px 14px; font-size:14px; color:#888;">Page {{ $creators->currentPage() }} of {{ $creators->lastPage() }}</span>
        @if($creators->hasMorePages())
            <a href="{{ $creators->nextPageUrl() }}">Next →</a>
        @else
            <span style="opacity:.4; padding:8px 14px; font-size:14px;">Next →</span>
        @endif
    </div>
    @endif
@endif
@endsection

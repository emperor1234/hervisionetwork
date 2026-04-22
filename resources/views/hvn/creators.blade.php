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
        <p>Be the first — <a href="/creator-signup" style="color:#F65F54;">join as a Creator</a>.</p>
    </div>
@else
    <div class="creator-grid">
    @foreach($creators as $user)
        @php
            $profile     = $user->creatorProfile;
            $displayName = $profile->display_name ?? $user->username;
            $photo       = $profile->profile_photo ?? null;
            $bio         = $profile->bio ?? null;
        @endphp
        <a href="/creators/{{ $user->username }}" class="hvn-card creator-card">
            <div class="creator-avatar">
                @if($photo)
                    <img src="{{ asset('storage/' . $photo) }}" alt="{{ $displayName }}">
                @else
                    {{ strtoupper(substr($displayName ?: '?', 0, 1)) }}
                @endif
            </div>
            <div class="creator-name">{{ $displayName }}</div>
            @if($bio)
                <div class="creator-bio">{{ Str::limit($bio, 100) }}</div>
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

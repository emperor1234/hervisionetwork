@extends('hvn.layout')
@section('title', ($profile->display_name ?: 'Creator') . ' — Her Vision Network')

@section('content')
<div style="max-width:640px; margin:0 auto;">
    <a href="/creators" style="color:#6c63ff; font-size:14px; text-decoration:none;">← Back to Creators</a>

    <div class="hvn-card" style="margin-top:20px; text-align:center; padding:40px 24px;">
        <div class="creator-avatar" style="width:100px; height:100px; font-size:36px; margin:0 auto 20px;">
            @if($profile->profile_photo)
                <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="{{ $profile->display_name }}">
            @else
                {{ strtoupper(substr($profile->display_name ?: '?', 0, 1)) }}
            @endif
        </div>
        <h1 style="font-size:24px; font-weight:500; color:#fff; margin-bottom:8px;">{{ $profile->display_name }}</h1>

        @if($profile->bio)
            <p style="color:#aaa; font-size:15px; line-height:1.7; margin-bottom:20px; max-width:500px; margin-left:auto; margin-right:auto;">{{ $profile->bio }}</p>
        @endif

        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:16px;">
            @if($profile->website_url)
                <a href="{{ $profile->website_url }}" target="_blank" rel="noopener"
                   style="color:#6c63ff; font-size:14px; text-decoration:none; background:#1e1b3a; padding:8px 16px; border-radius:20px;">
                    🌐 Website
                </a>
            @endif
            @if($profile->contact_email)
                <a href="mailto:{{ $profile->contact_email }}"
                   style="color:#6c63ff; font-size:14px; text-decoration:none; background:#1e1b3a; padding:8px 16px; border-radius:20px;">
                    ✉️ Contact
                </a>
            @endif
            @if($profile->social_links)
                @foreach((array) $profile->social_links as $platform => $url)
                    @if($url)
                        <a href="{{ $url }}" target="_blank" rel="noopener"
                           style="color:#6c63ff; font-size:14px; text-decoration:none; background:#1e1b3a; padding:8px 16px; border-radius:20px; text-transform:capitalize;">
                            {{ $platform }}
                        </a>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

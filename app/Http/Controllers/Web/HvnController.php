<?php

namespace App\Http\Controllers\Web;

use App\CommunityPost;
use App\CreatorProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HvnController extends Controller
{
    public function creatorSignup()
    {
        if (auth()->check()) {
            return redirect('/');
        }

        return view('hvn.creator-signup');
    }

    public function community(Request $request)
    {
        $posts = CommunityPost::with(['user:id,username'])
            ->published()
            ->withCount(['comments', 'likes'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('hvn.community', compact('posts'));
    }

    public function creators(Request $request)
    {
        $creators = CreatorProfile::whereHas('user', function ($q) {
                $q->where('role', 'creator');
            })
            ->orderBy('display_name')
            ->paginate(20);

        return view('hvn.creators', compact('creators'));
    }

    public function creatorProfile(int $userId)
    {
        $profile = CreatorProfile::whereHas('user', function ($q) {
                $q->where('role', 'creator');
            })
            ->where('user_id', $userId)
            ->firstOrFail();

        return view('hvn.creator-profile', compact('profile'));
    }
}

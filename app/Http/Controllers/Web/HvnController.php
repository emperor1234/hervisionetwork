<?php

namespace App\Http\Controllers\Web;

use App\CommunityPost;
use App\CreatorProfile;
use Common\Core\BaseController as Controller;
use App\CommunityComment;
use Illuminate\Http\JsonResponse;
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
        $creators = \App\User::where('role', 'creator')
            ->with('creatorProfile')
            ->orderBy('username')
            ->paginate(20);

        return view('hvn.creators', compact('creators'));
    }

    public function communityStore(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string|max:10000',
        ]);

        $post = CommunityPost::create([
            'user_id' => auth()->id(),
            'title'   => $request->input('title'),
            'body'    => $request->input('body'),
            'status'  => 'published',
        ]);

        return response()->json(['post' => $post], 201);
    }

    public function communityShow(Request $request, int $postId)
    {
        $post = CommunityPost::with(['user:id,username', 'comments.user:id,username'])
            ->published()
            ->withCount(['comments', 'likes'])
            ->findOrFail($postId);

        return view('hvn.community-post', compact('post'));
    }

    public function commentStore(Request $request, int $postId): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $request->validate(['body' => 'required|string|max:5000']);

        $post = CommunityPost::published()->findOrFail($postId);

        $comment = CommunityComment::create([
            'post_id'    => $post->id,
            'user_id'    => auth()->id(),
            'body'       => $request->input('body'),
            'created_at' => now(),
        ]);

        return response()->json(['comment' => $comment->load('user:id,username')], 201);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
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

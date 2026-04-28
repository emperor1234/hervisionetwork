<?php

namespace App\Http\Controllers\Web;

use App\CommunityComment;
use App\CommunityLike;
use App\CommunityPost;
use App\NewsArticle;
use App\Title;
use App\User;
use App\Video;
use Common\Core\BaseController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HvnAdminController extends Controller
{
    private function adminOrAbort()
    {
        if (!auth()->check()) {
            return redirect('/login');
        }
        $user = auth()->user();
        if (!$user->hasPermission('admin')) {
            abort(403, 'Admin access required.');
        }
        return $user;
    }

    public function dashboard()
    {
        $result = $this->adminOrAbort();
        if ($result instanceof \Illuminate\Http\RedirectResponse) return $result;

        $stats = [
            'creators'      => User::where('role', 'creator')->count(),
            'viewers'       => User::where('role', 'viewer')->count(),
            'posts'         => CommunityPost::count(),
            'published'     => CommunityPost::where('status', 'published')->count(),
            'content'       => Title::whereHas('videos', fn($q) => $q->whereNotNull('user_id'))->count(),
            'news'          => NewsArticle::count(),
        ];

        $recentCreators = User::where('role', 'creator')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'username', 'email', 'created_at']);

        $recentPosts = CommunityPost::with('user:id,username')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'title', 'status', 'user_id', 'created_at']);

        return view('hvn.admin.dashboard', compact('stats', 'recentCreators', 'recentPosts'));
    }

    public function creators(Request $request)
    {
        $result = $this->adminOrAbort();
        if ($result instanceof \Illuminate\Http\RedirectResponse) return $result;

        $search = $request->input('q');
        $query = User::where('role', 'creator')
            ->with('creatorProfile')
            ->orderByDesc('created_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $creators = $query->paginate(20)->withQueryString();

        return view('hvn.admin.creators', compact('creators', 'search'));
    }

    public function community(Request $request)
    {
        $result = $this->adminOrAbort();
        if ($result instanceof \Illuminate\Http\RedirectResponse) return $result;

        $search = $request->input('q');
        $query = CommunityPost::with('user:id,username')
            ->withCount(['comments', 'likes'])
            ->orderByDesc('created_at');

        if ($search) {
            $query->where('title', 'like', "%$search%");
        }

        $posts = $query->paginate(20)->withQueryString();

        return view('hvn.admin.community', compact('posts', 'search'));
    }

    public function content(Request $request)
    {
        $result = $this->adminOrAbort();
        if ($result instanceof \Illuminate\Http\RedirectResponse) return $result;

        $titles = Title::whereHas('videos', fn($q) => $q->whereNotNull('user_id'))
            ->with([
                'videos' => fn($q) => $q->whereNotNull('user_id')
                    ->select('id', 'title_id', 'user_id', 'source', 'url', 'name'),
            ])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('hvn.admin.content', compact('titles'));
    }

    public function deletePost(Request $request, int $postId)
    {
        $result = $this->adminOrAbort();
        if ($result instanceof \Illuminate\Http\RedirectResponse) return $result;

        $post = CommunityPost::findOrFail($postId);
        CommunityComment::where('post_id', $postId)->delete();
        CommunityLike::where('post_id', $postId)->delete();
        $post->delete();

        return back()->with('flash', ['type' => 'success', 'message' => 'Post deleted.']);
    }

    public function hidePost(Request $request, int $postId)
    {
        $result = $this->adminOrAbort();
        if ($result instanceof \Illuminate\Http\RedirectResponse) return $result;

        $post = CommunityPost::findOrFail($postId);
        $post->status = ($post->status === 'published') ? 'removed' : 'published';
        $post->save();

        $label = $post->status === 'published' ? 'Post restored.' : 'Post hidden.';
        return back()->with('flash', ['type' => 'success', 'message' => $label]);
    }

    public function deleteContent(Request $request, int $titleId)
    {
        $result = $this->adminOrAbort();
        if ($result instanceof \Illuminate\Http\RedirectResponse) return $result;

        $title = Title::findOrFail($titleId);
        $videos = Video::where('title_id', $titleId)->get();

        foreach ($videos as $video) {
            if ($video->source === 'local' && $video->url) {
                $rel = ltrim(str_replace('/storage/', '', $video->url), '/');
                Storage::disk('public')->delete($rel);
            }
            $video->delete();
        }

        if ($title->poster && strpos($title->poster, '/storage/creator_content') === 0) {
            $rel = ltrim(str_replace('/storage/', '', $title->poster), '/');
            Storage::disk('public')->delete($rel);
        }
        $title->delete();

        return back()->with('flash', ['type' => 'success', 'message' => 'Content deleted.']);
    }

    public function toggleCreator(Request $request, int $userId)
    {
        $result = $this->adminOrAbort();
        if ($result instanceof \Illuminate\Http\RedirectResponse) return $result;

        $creator = User::findOrFail($userId);
        $creator->role = ($creator->role === 'creator') ? 'viewer' : 'creator';
        $creator->save();

        $label = $creator->role === 'creator' ? 'Creator access restored.' : 'Creator access revoked.';
        return back()->with('flash', ['type' => 'success', 'message' => $label]);
    }
}

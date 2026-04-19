<?php

namespace App\Http\Controllers;

use App\CommunityComment;
use App\CommunityLike;
use App\CommunityPost;
use Common\Core\BaseController;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommunityController extends BaseController
{
    /**
     * GET /api/v1/community/posts
     * Paginated list of published posts with author info and like/comment counts.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 15), 50);

        $posts = CommunityPost::with(['user:id,username,avatar'])
            ->published()
            ->withCount(['comments', 'likes'])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return $this->success(['pagination' => $posts]);
    }

    /**
     * GET /api/v1/community/posts/{postId}
     * Single post with its comments and like count.
     */
    public function show(Request $request, int $postId): JsonResponse
    {
        $post = CommunityPost::with(['user:id,username,avatar'])
            ->published()
            ->withCount('likes')
            ->find($postId);

        if (!$post) {
            return $this->error('Post not found.', [], 404);
        }

        $comments = CommunityComment::with(['user:id,username,avatar'])
            ->where('post_id', $postId)
            ->orderBy('created_at')
            ->get();

        $userLiked = CommunityLike::where('post_id', $postId)
            ->where('user_id', $request->user()->id)
            ->exists();

        return $this->success([
            'post'       => $post,
            'comments'   => $comments,
            'user_liked' => $userLiked,
        ]);
    }

    /**
     * POST /api/v1/community/posts
     * Create a new post. Both roles allowed.
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'body'  => 'required|string|max:10000',
        ]);

        $post = CommunityPost::create([
            'user_id' => $request->user()->id,
            'title'   => $request->input('title'),
            'body'    => $request->input('body'),
            'status'  => 'published',
        ]);

        $post->load('user:id,username,avatar');

        return $this->success(['post' => $post], 201);
    }

    /**
     * POST /api/v1/community/posts/{postId}/comments
     * Add a comment to a post.
     */
    public function addComment(Request $request, int $postId): JsonResponse
    {
        $this->validate($request, [
            'body' => 'required|string|max:1000',
        ]);

        $post = CommunityPost::published()->find($postId);

        if (!$post) {
            return $this->error('Post not found.', [], 404);
        }

        $comment = CommunityComment::create([
            'post_id'    => $postId,
            'user_id'    => $request->user()->id,
            'body'       => $request->input('body'),
            'created_at' => now(),
        ]);

        $comment->load('user:id,username,avatar');

        return $this->success(['comment' => $comment], 201);
    }

    /**
     * POST /api/v1/community/posts/{postId}/like
     * Toggle like on a post. Returns action taken and updated count.
     */
    public function toggleLike(Request $request, int $postId): JsonResponse
    {
        $post = CommunityPost::published()->find($postId);

        if (!$post) {
            return $this->error('Post not found.', [], 404);
        }

        $userId = $request->user()->id;
        $existing = CommunityLike::where('post_id', $postId)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $action = 'unliked';
        } else {
            try {
                CommunityLike::create(['post_id' => $postId, 'user_id' => $userId, 'created_at' => now()]);
                $action = 'liked';
            } catch (QueryException $e) {
                // Duplicate like from a race condition — treat as already liked
                $action = 'liked';
            }
        }

        $count = CommunityLike::where('post_id', $postId)->count();

        return $this->success(['action' => $action, 'likes_count' => $count]);
    }
}

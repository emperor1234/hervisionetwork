<?php

namespace App\Http\Controllers;

use App\Title;
use App\Video;
use Common\Core\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CreatorContentController extends BaseController
{
    /**
     * GET /api/v1/creator/content
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $titles = Title::whereHas('videos', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->with(['videos' => function ($q) use ($userId) {
                $q->where('user_id', $userId)->select('id', 'title_id', 'url', 'type', 'category', 'source');
            }])
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->success(['titles' => $titles]);
    }

    /**
     * POST /api/v1/creator/content
     * Accepts multipart/form-data with optional video_file and cover image.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $this->validate($request, [
            'title'       => 'required|string|min:2|max:250',
            'type'        => 'required|in:movie,short,series,documentary',
            'year'        => 'nullable|integer|min:1900|max:2099',
            'description' => 'nullable|string|max:5000',
            'video_url'   => 'nullable|string|max:1000',
            'video_file'  => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo|max:512000',
            'cover'       => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if (!$request->filled('video_url') && !$request->hasFile('video_file')) {
            return response()->json([
                'errors' => ['video_url' => ['Provide a video URL or upload a video file.']],
            ], 422);
        }

        $posterPath = $request->file('cover')->store('creator_content/covers', 'public');

        $record = new Title();
        $record->title  = $request->input('title');
        $record->type   = $request->input('type');
        $record->year   = $request->input('year');
        $record->plot   = $request->input('description');
        $record->poster = '/storage/' . $posterPath;
        $record->save();

        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('creator_content/videos', 'public');
            $videoUrl  = '/storage/' . $videoPath;
            $source    = 'local';
        } else {
            $videoUrl = $request->input('video_url');
            $source   = 'external';
        }

        Video::create([
            'title_id' => $record->id,
            'user_id'  => $user->id,
            'name'     => $record->title,
            'url'      => $videoUrl,
            'type'     => 'video',
            'category' => 'full',
            'language' => 'en',
            'source'   => $source,
            'approved' => 1,
            'order'    => 1,
        ]);

        return response()->json(['title' => $record], 201);
    }

    /**
     * DELETE /api/v1/creator/content/{id}
     */
    public function destroy(Request $request, int $titleId): JsonResponse
    {
        $userId = $request->user()->id;

        $videos = Video::where('title_id', $titleId)->where('user_id', $userId)->get();
        if ($videos->isEmpty()) {
            return response()->json(['message' => 'Not found or unauthorized.'], 404);
        }

        foreach ($videos as $video) {
            if ($video->source === 'local' && $video->url) {
                $rel = ltrim(str_replace('/storage/', '', $video->url), '/');
                Storage::disk('public')->delete($rel);
            }
            $video->delete();
        }

        $title = Title::find($titleId);
        if ($title && !Video::where('title_id', $titleId)->exists()) {
            if ($title->poster && strpos($title->poster, '/storage/creator_content') === 0) {
                $rel = ltrim(str_replace('/storage/', '', $title->poster), '/');
                Storage::disk('public')->delete($rel);
            }
            $title->delete();
        }

        return response()->json(['deleted' => true]);
    }
}

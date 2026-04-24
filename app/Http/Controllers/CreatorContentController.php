<?php

namespace App\Http\Controllers;

use App\Title;
use App\Video;
use Common\Core\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreatorContentController extends BaseController
{
    /**
     * GET /api/v1/creator/content
     * Returns the authenticated creator's uploaded titles.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $titles = Title::whereHas('videos', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->with(['videos' => function ($q) use ($userId) {
                $q->where('user_id', $userId)->select('id', 'title_id', 'url', 'type', 'category');
            }])
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->success(['titles' => $titles]);
    }

    /**
     * POST /api/v1/creator/content
     * Creates a title and attaches a video for the authenticated creator.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $this->validate($request, [
            'title'       => 'required|string|min:2|max:250',
            'type'        => 'required|in:movie,short,series,documentary',
            'year'        => 'nullable|integer|min:1900|max:2099',
            'description' => 'nullable|string|max:5000',
            'video_url'   => 'required|string|max:1000',
        ]);

        $record = new Title();
        $record->title = $request->input('title');
        $record->type  = $request->input('type');
        $record->year  = $request->input('year');
        $record->plot  = $request->input('description');
        $record->save();

        Video::create([
            'title_id' => $record->id,
            'user_id'  => $user->id,
            'name'     => $request->input('title'),
            'url'      => $request->input('video_url'),
            'type'     => 'video',
            'category' => 'full',
            'language' => 'en',
            'source'   => 'external',
            'approved' => 1,
            'order'    => 1,
        ]);

        return response()->json(['title' => $record], 201);
    }

    /**
     * DELETE /api/v1/creator/content/{id}
     * Removes the creator's video and deletes the title if no other videos remain.
     */
    public function destroy(Request $request, int $titleId): JsonResponse
    {
        $userId = $request->user()->id;

        $owned = Video::where('title_id', $titleId)->where('user_id', $userId)->exists();
        if (!$owned) {
            return response()->json(['message' => 'Not found or unauthorized.'], 404);
        }

        Video::where('title_id', $titleId)->where('user_id', $userId)->delete();

        if (!Video::where('title_id', $titleId)->exists()) {
            Title::find($titleId)->delete();
        }

        return response()->json(['deleted' => true]);
    }
}

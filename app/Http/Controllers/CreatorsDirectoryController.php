<?php

namespace App\Http\Controllers;

use App\CreatorProfile;
use Common\Core\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreatorsDirectoryController extends BaseController
{
    /**
     * GET /api/v1/creators
     * Paginated list of all public creator profiles.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 20), 50);

        $creators = CreatorProfile::with(['user:id,username,avatar'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'creator');
            })
            ->orderBy('display_name')
            ->paginate($perPage);

        return $this->success(['pagination' => $creators]);
    }

    /**
     * GET /api/v1/creators/{userId}
     * View a single creator's public profile.
     */
    public function show(int $userId): JsonResponse
    {
        $profile = CreatorProfile::with(['user:id,username,avatar'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'creator');
            })
            ->where('user_id', $userId)
            ->first();

        if (!$profile) {
            return $this->error('Creator profile not found.', [], 404);
        }

        return $this->success(['profile' => $profile]);
    }
}

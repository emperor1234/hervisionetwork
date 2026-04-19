<?php

namespace App\Http\Controllers;

use App\CreatorProfile;
use Common\Core\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CreatorProfileController extends BaseController
{
    private const ALLOWED_SOCIAL_KEYS = ['instagram', 'youtube', 'twitter', 'facebook', 'tiktok', 'linkedin'];

    /**
     * GET /api/v1/creator/profile
     */
    public function show(Request $request): JsonResponse
    {
        $profile = CreatorProfile::where('user_id', $request->user()->id)->first();

        return $this->success(['profile' => $profile]);
    }

    /**
     * POST /api/v1/creator/profile
     * Creates or updates the authenticated creator's profile.
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'display_name'    => 'required|string|max:150',
            'bio'             => 'nullable|string|max:2000',
            'website_url'     => 'nullable|url|max:255',
            'contact_email'   => 'nullable|email|max:150',
            'social_links'    => 'nullable|array',
            'social_links.*'  => 'nullable|url|max:255',
            'profile_photo'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $userId = $request->user()->id;
        $data   = $request->only(['display_name', 'bio', 'website_url', 'contact_email']);

        // Strip any unrecognised social link keys
        if ($request->filled('social_links')) {
            $data['social_links'] = array_intersect_key(
                $request->input('social_links'),
                array_flip(self::ALLOWED_SOCIAL_KEYS)
            );
        }

        if ($request->hasFile('profile_photo')) {
            // Delete previous photo to avoid orphaned files
            $existing = CreatorProfile::where('user_id', $userId)->first();
            if ($existing && $existing->profile_photo) {
                Storage::disk('public')->delete($existing->profile_photo);
            }

            $data['profile_photo'] = $request->file('profile_photo')
                ->store('creator_photos', 'public');
        }

        $profile = CreatorProfile::updateOrCreate(['user_id' => $userId], $data);

        return $this->success(['profile' => $profile]);
    }
}

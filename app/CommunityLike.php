<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityLike extends Model
{
    public $timestamps = false;

    protected $fillable = ['post_id', 'user_id'];

    protected $casts = ['created_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(CommunityPost::class, 'post_id');
    }
}

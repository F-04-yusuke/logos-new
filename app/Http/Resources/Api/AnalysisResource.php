<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalysisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'user_id'      => $this->user_id,
            'topic_id'     => $this->topic_id,
            'title'        => $this->title,
            'type'         => $this->type,
            'data'         => $this->data,
            'is_published' => $this->is_published,
            'supplement'   => $this->supplement,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            // ロード済みリレーションのみ含む
            'user'  => $this->whenLoaded('user', fn() => [
                'id'     => $this->user->id,
                'name'   => $this->user->name,
                'avatar' => $this->user->avatar,
            ]),
            'topic' => $this->whenLoaded('topic', fn() => [
                'id'    => $this->topic->id,
                'title' => $this->topic->title,
            ]),
            'likes_count'    => $this->whenCounted('likes'),
            // show エンドポイントで $analysis->is_liked_by_me = ... と動的にセットして渡す
            'is_liked_by_me' => $this->when(
                isset($this->resource->is_liked_by_me),
                fn() => $this->resource->is_liked_by_me
            ),
        ];
    }
}

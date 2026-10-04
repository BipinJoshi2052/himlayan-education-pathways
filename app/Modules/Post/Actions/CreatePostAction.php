<?php

declare(strict_types=1);

namespace App\Modules\Post\Actions;

use App\Common\Helpers\SlugGenerator;
use App\Modules\Post\DTOs\PostData;
use App\Modules\Post\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final readonly class CreatePostAction
{
    public function handle(PostData $data, ?UploadedFile $featuredImage = null): Post
    {
        return DB::transaction(function () use ($data, $featuredImage) {
            $post = new Post;

            $post->type = $data->type;
            $post->title = $data->title;
            $post->slug = $data->slug ?: SlugGenerator::make(Post::class, $data->primaryTitle());
            $post->summary = $data->summary;
            $post->content = $data->content;
            $post->is_featured = $data->isFeatured;
            $post->status = $data->status;
            $post->published_at = $data->publishedAt;
            $post->sort_order = $data->sortOrder;
            $post->meta_title = $data->metaTitle;
            $post->meta_description = $data->metaDescription;
            $post->meta_keywords = $data->metaKeywords;
            $post->created_by = auth()->id();
            $post->save();

            if ($featuredImage !== null) {
                $post->addMedia($featuredImage)->toMediaCollection('featured_image');
            }

            return $post;
        });
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    /**
     * Toggle like for a post (like or unlike)
     */
    public function toggle(Request $request, Post $post): JsonResponse
    {
        $action = $request->input('action'); // 'like' or 'unlike'

        if ($action === 'like') {
            // Increment the likes count
            $post->likes_count = $post->likes_count + 1;
            $post->save();
            
            return response()->json([
                'success' => true,
                'action' => 'liked',
                'likes_count' => $post->likes_count,
            ]);
        } elseif ($action === 'unlike') {
            // Decrement the likes count (but don't go below 0)
            if ($post->likes_count > 0) {
                $post->likes_count = $post->likes_count - 1;
                $post->save();
            }
            
            return response()->json([
                'success' => true,
                'action' => 'unliked',
                'likes_count' => $post->likes_count,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid action',
        ], 400);
    }

    /**
     * Get the current likes count for a post
     */
    public function count(Post $post): JsonResponse
    {
        return response()->json([
            'success' => true,
            'likes_count' => $post->likes_count,
        ]);
    }
}

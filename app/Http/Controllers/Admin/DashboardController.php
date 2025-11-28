<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Total counts
        $totalPosts = Post::count();
        $totalPublished = Post::published()->count();
        $totalDrafts = Post::where('is_draft', true)->count();
        $totalUsers = User::count();
        $totalCategories = Category::count();
        $totalTags = Tag::count();
        $totalLikes = Post::sum('likes_count');

        // Recent posts
        $recentPosts = Post::with('author')
            ->latest()
            ->take(5)
            ->get();

        // Chart data: Posts per month for the last 6 months
        $chartData = $this->getPostsPerMonth();

        // Popular posts (by likes)
        $popularPosts = Post::published()
            ->orderBy('likes_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPosts',
            'totalPublished',
            'totalDrafts',
            'totalUsers',
            'totalCategories',
            'totalTags',
            'totalLikes',
            'recentPosts',
            'chartData',
            'popularPosts'
        ));
    }

    private function getPostsPerMonth(): array
    {
        $months = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->translatedFormat('M Y'); // e.g., "Nov 2024"
            
            $count = Post::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $months[] = $monthName;
            $counts[] = $count;
        }

        return [
            'labels' => $months,
            'data' => $counts,
        ];
    }
}

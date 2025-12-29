<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClearPostsAndMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:posts-media 
                            {--files : Also delete associated files from storage}
                            {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all posts and media library data from the database and optionally delete associated files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete all posts and media data? This action cannot be undone!', true)) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        $this->info('Starting deletion process...');

        // Start transaction for safety
        DB::beginTransaction();

        try {
            // Count records before deletion
            $postsCount = Post::count();
            $mediaCount = Media::count();

            $this->info("Found {$postsCount} posts and {$mediaCount} media records.");

            // Delete posts (pivot tables will be cleared automatically due to cascade)
            if ($postsCount > 0) {
                $this->info('Deleting posts...');
                Post::query()->delete();
                $this->info("✓ Deleted {$postsCount} posts.");
            }

            // Delete media records
            if ($mediaCount > 0) {
                $this->info('Deleting media records...');
                Media::query()->delete();
                $this->info("✓ Deleted {$mediaCount} media records.");
            }

            // Delete files if option is set
            if ($this->option('files')) {
                $this->info('Deleting associated files from storage...');
                
                // Delete post images
                if (Storage::disk('public')->exists('posts')) {
                    $deletedPosts = Storage::disk('public')->deleteDirectory('posts');
                    $this->info('✓ Deleted posts images directory.');
                }

                // Delete media files
                if (Storage::disk('public')->exists('media')) {
                    $deletedMedia = Storage::disk('public')->deleteDirectory('media');
                    $this->info('✓ Deleted media files directory.');
                }

                // Delete uploads (CKEditor uploads)
                if (Storage::disk('public')->exists('uploads')) {
                    $deletedUploads = Storage::disk('public')->deleteDirectory('uploads');
                    $this->info('✓ Deleted uploads directory.');
                }
            }

            DB::commit();
            $this->info('');
            $this->info('✓ Successfully deleted all posts and media data!');
            
            if (!$this->option('files')) {
                $this->warn('Note: Associated files were not deleted. Use --files flag to delete them as well.');
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}


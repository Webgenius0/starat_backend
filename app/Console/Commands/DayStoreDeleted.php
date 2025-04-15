<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DayStoreDeleted extends Command
{
    protected $signature = 'daystorie:clean-up';
    protected $description = 'Delete daystories older than one day, their files, and related reacts';

    public function handle()
    {
        // Get stories older than 1 day
        $oldStories = DB::table('stories')
            ->where('created_at', '<', Carbon::now()->subDay())
            ->get();

        foreach ($oldStories as $story) {
            // Delete associated reacts for the story
            DB::table('story_reacts')->where('story_id', $story->id)->delete();
            $this->info("Deleted reacts for story ID: {$story->id}");

            if (isset($story->file_url) && File::exists(public_path($story->file_url))) {
                File::delete(public_path($story->file_url));
            }

            // Ensure you're deleting the correct record from the 'stories' table
            DB::table('stories')->where('id', $story->id)->delete();
            $this->info("Deleted story ID: {$story->id}");
        }

        $this->info("✅ Cleanup complete.");
    }
}

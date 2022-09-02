<?php

namespace App\Console\Commands;

use App\Helpers\UtilsHelper;
use Illuminate\Console\Command;

class FetchNewPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'squared:fetch-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch New Posts from https://sq1-api-test.herokuapp.com/posts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        UtilsHelper::fetchPostsScheduler();
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Session;

class ClearSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'squared:clear-session';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will Clear User Sessions';

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
     * @return void
     */
    public function handle(): void
    {
        Session::getHandler()->gc(0);
    }
}

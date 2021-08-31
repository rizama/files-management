<?php

namespace App\Console\Commands;

use App\Models\File;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClearAttachment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attachment:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Attachment Task After 1 Month';

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
     * @return int
     */
    public function handle()
    {
        $attachments = File::where('type', 'attachment')
            ->where('created_at', '<=', now()->subMonth())
            ->get();
        $count = count($attachments);
        $attachments->each->delete();   

        if ($count) {
            Log::info(now() . "Attachment Clear");
        }
    }
}

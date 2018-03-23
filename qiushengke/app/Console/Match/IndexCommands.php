<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/20
 * Time: 上午11:47
 */

namespace App\Console\Match;


use App\Http\Controllers\PC\Match\MatchController;
use App\Models\QSK\Match\Match;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class IndexCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'index缓存';

    /**
     * Create a new command instance.
     * HotMatchCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        MatchController::curlToHtml();
    }
}
<?php

namespace Marshmallow\Server\ProjectUsage\Console\Commands;

use Illuminate\Console\Command;
use Marshmallow\Server\ProjectUsage\DataGenerator;

class ShowProjectUsageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marshmallow:show-project-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the project usage object that will be send to the endpoint';

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
     * @return mixed
     */
    public function handle()
    {
        if (! DataGenerator::shouldRun()) {
            $this->line("Marshmallow Publish Packages: <error>Not run because the .env file is not filled</error>");
        } else {
            $output = (new DataGenerator($this))
                            ->generate()
                            ->output();

            dd(json_decode($output));
        }
    }
}

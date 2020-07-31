<?php

namespace Marshmallow\Server\ProjectUsage\Console\Commands;

use Illuminate\Console\Command;
use Marshmallow\Server\ProjectUsage\DataGenerator;

class PublishPackageUsageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marshmallow:publish-package-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the package usage object to your endpoint';

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
    	if (!DataGenerator::shouldRun()) {
    		return 1;
    	}
    	(new DataGenerator)
				->publishPackages();
    }
}

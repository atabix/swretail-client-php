<?php

namespace SWRetail\Commands;

use Illuminate\Console\Command;
use SWRetail\Http\Client;

class SWRetailVersionCommand extends Command
{
    protected $signature = 'swretail:version ';

    protected $description = 'Calls the /version endpoint for SWRetail and display\'s the current version';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $client = new Client();

        try {
            $response = $client->apiRequest('GET', 'version');

            $this->output->write('The version of SWRetail API is: ');
            $this->info($response->json->version);
        } catch (\Exception $e) {
            $this->line($e->getMessage());
        }
    }
}

<?php

namespace SWRetail\Commands;

use Illuminate\Console\Command;

class SWRetailConfigCommand extends Command
{
    protected $signature = 'swretail:config';

    protected $description = 'Display the current config for SWRetail';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->line('--------------------------------');
        $this->line('SWRetail config');
        $this->line('--------------------------------');
        $this->output->write('Server:    ', false);
        $this->info(config('swretail.endpoint'));
        $this->output->write('Username:  ', false);
        $this->info(config('swretail.username'));
        $this->output->write('Password:  ', false);
        $this->info(config('swretail.password'));
        $this->line('--------------------------------');
    }
}

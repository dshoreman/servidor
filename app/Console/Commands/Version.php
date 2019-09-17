<?php

namespace Servidor\Console\Commands;

use Illuminate\Console\Command;

class Version extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'servidor:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print the current version of Servidor.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Servidor v' . SERVIDOR_VERSION);
    }
}

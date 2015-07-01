<?php
namespace EspadaV8\ClosureTable\Console;

use EspadaV8\ClosureTable\ClosureTableServiceProvider as CT;
use Illuminate\Console\Command;

/**
 * Basic ClosureTable command, outputs information about the library in short.
 *
 * @package EspadaV8\ClosureTable\Console
 */
class ClosureTableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'closuretable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get ClosureTable version notice.';

    /**
     * Executes console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->info('ClosureTable v' . CT::VERSION);
        $this->line('Closure Table database design pattern implementation for Laravel framework.');
        $this->comment('Copyright (c) 2013-2014 Jan Iwanow');
    }
}

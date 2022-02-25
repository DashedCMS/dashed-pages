<?php

namespace Qubiqx\QcommercePages\Commands;

use Illuminate\Console\Command;

class QcommercePagesCommand extends Command
{
    public $signature = 'qcommerce-pages';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

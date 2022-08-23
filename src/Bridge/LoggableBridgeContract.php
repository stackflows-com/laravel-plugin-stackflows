<?php

namespace Stackflows\Bridge;

use Illuminate\Support\Collection;

interface LoggableBridgeContract
{
    /**
     * @return Collection
     */
    public function logs(int $offset = 0, int $limit = 100): Collection;
}

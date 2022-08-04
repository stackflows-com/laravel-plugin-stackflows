<?php

namespace Stackflows\Collections;

use Illuminate\Support\Collection;

class PaginatedCollection extends Collection
{
    protected int $total;

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal(int $total): void
    {
        $this->total = $total;
    }
}

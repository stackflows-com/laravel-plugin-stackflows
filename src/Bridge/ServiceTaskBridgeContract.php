<?php

namespace Stackflows\Bridge;

use Illuminate\Support\Collection;
use Stackflows\DataTransfer\Collections\DataPointCollection;
use Stackflows\DataTransfer\Types\ServiceTaskType;

interface ServiceTaskBridgeContract
{
    /**
     * @return Collection|ServiceTaskType[]
     */
    public function getAll(): Collection;

    /**
     * @param string $reference
     * @return ServiceTaskType
     */
    public function get(string $reference): ServiceTaskType;

    /**
     * @param string $topic
     * @param string $lock
     * @param int $duration
     * @param int $limit
     * @return Collection|ServiceTaskType[]
     */
    public function lockTopic(string $topic, string $lock, int $duration = 300, int $limit = 100): Collection;

    /**
     * @param string $reference
     * @param string $lock
     * @param int $duration
     * @return ServiceTaskType
     */
    public function lock(string $reference, string $lock, int $duration = 300): ServiceTaskType;

    /**
     * @param string $reference
     * @return ServiceTaskType
     */
    public function unlock(string $reference): ServiceTaskType;

    /**
     * @param string $lock
     * @param string $reference
     * @return ServiceTaskType
     */
    public function serve(string $lock, string $reference, DataPointCollection $dataObject = null): ServiceTaskType;

    /**
     * @return Collection
     */
    public function logs(): Collection;
}

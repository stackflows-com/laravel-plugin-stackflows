<?php

namespace Stackflows\Bridge;

interface EnvironmentBridgeContract
{
    public function save(Environment $environment): Environment;

    public function create(Environment $environment): Environment;

    public function exists(Environment $environment): bool;

    public function update(Environment $environment): Environment;

    public function delete(Environment $environment): Environment;
}

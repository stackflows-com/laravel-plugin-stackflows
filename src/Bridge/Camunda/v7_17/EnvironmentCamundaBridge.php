<?php

namespace Stackflows\Bridge\Camunda\v7_17;

use Stackflows\Bridge\AbstractBridge;
use Stackflows\Bridge\EnvironmentBridgeContract;
use Stackflows\Types\EnvironmentType;
use Stackflows\Clients\Camunda\v7_17\Api\TenantApi;
use Stackflows\Clients\Camunda\v7_17\Model\TenantDto;

class EnvironmentCamundaBridge extends AbstractBridge implements EnvironmentBridgeContract
{
    public function __construct(
        protected Environment $environment,
        protected TenantApi $tenantApi
    ) {

    }

    public function save(Environment $environment): Environment
    {
        if ($this->exists($environment)) {
            return $this->update($environment);
        }

        return $this->create($environment);
    }

    public function create(Environment $environment): Environment
    {
        $this->tenantApi->createTenant(new TenantDto([
            'id' => $environment->getAlphanumericKey(),
            'name' => $environment->getAlphanumericKey(),
        ]));

        $tenant = $this->tenantApi->getTenant($environment->getAlphanumericKey());

        $environment->setAttribute('engine_reference', $tenant->getId());

        return $environment;
    }

    public function exists(Environment $environment): bool
    {
        return $environment->getAttribute('engine_reference')
            && $this->tenantApi->getTenantCount($environment->getAttribute('engine_reference'))->getCount();
    }

    public function update(Environment $environment): Environment
    {
        $this->tenantApi->updateTenant(
            $environment->getAttribute('engine_reference'),
            new TenantDto([
                'id' => $environment->getAttribute('engine_reference'),
                'name' => $environment->getTitle(),
            ])
        );

        return $environment;
    }

    public function delete(Environment $environment): Environment
    {
        $this->tenantApi->deleteTenant($environment->getAttribute('engine_reference'));

        return $environment;
    }
}

<?php

namespace Stackflows\StackflowsPlugin\Services\UserTask;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stackflows\GatewayApi\Api\UserTaskApi;
use Stackflows\StackflowsPlugin\Auth\BackofficeAuth;
use Stackflows\StackflowsPlugin\Exceptions\InvalidCredentials;
use Stackflows\StackflowsPlugin\Stackflows;

abstract class CompleteUserTaskJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The User Task instance.
     */
    protected InternalStackflowsUserTaskModel $task;

    /**
     * Create a new job instance.
     *
     * @param InternalStackflowsUserTaskModel $task
     */
    public function __construct(InternalStackflowsUserTaskModel $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @param Stackflows $client
     * @return void
     * @throws \Stackflows\GatewayApi\ApiException
     */
    public function handle(Stackflows $client)
    {
        $this->authenticate($client->getAuth());

        $channel = $client->getUserTaskChannel();
        $this->beforeHandle($channel->getApi());
        $channel->complete($this->task->getStackflowsUserTaskKey());

        $this->afterHandle();
    }

    public function beforeHandle(UserTaskApi $api)
    {
    }

    public function afterHandle()
    {
    }

    /**
     * @throws InvalidCredentials
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function authenticate(BackofficeAuth $auth)
    {
        if ($auth->check()) {
            return;
        }

        if ($auth->attempt(config('stackflows.email'), config('stackflows.password'))) {
            return;
        }

        throw InvalidCredentials::emailOrPassword();
    }
}

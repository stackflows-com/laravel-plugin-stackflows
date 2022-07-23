<?php

namespace Stackflows\BusinessProcesses\ServiceTasks\Outputs;

use Stackflows\Types\SubmissionType;

abstract class AbstractServiceTaskOutput implements ServiceTaskOutputInterface
{
    private SubmissionType $submission;

    public function __construct(SubmissionType $submission)
    {
        $this->submission = $submission;
    }

    /**
     * @return SubmissionType
     */
    public function getSubmission(): SubmissionType
    {
        return $this->submission;
    }
}

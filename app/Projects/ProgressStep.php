<?php

namespace Servidor\Projects;

class ProgressStep
{
    public const REASON_EXISTS = 'it already exists';
    public const REASON_MISSING_DATA = 'some required data is missing';
    public const REASON_NOT_ENABLED = 'project is not enabled';
    public const REASON_REQUIRED = "it's not required";

    public const STATUS_PENDING = 'pending';
    public const STATUS_SKIPPED = 'skipped';
    public const STATUS_DONE = 'complete';

    public string $name;

    public string $text;

    public int $progress;

    public string $status;

    public string $reason = '';

    public function __construct(string $name, string $text, int $percentWhenComplete)
    {
        $this->progress = $percentWhenComplete;
        $this->status = self::STATUS_PENDING;
        $this->name = $name;
        $this->text = $text;
    }

    public function complete(string $reason = ''): self
    {
        $this->status = self::STATUS_DONE;
        $this->reason = $reason;

        return $this;
    }

    public function skip(string $reason = ''): self
    {
        $this->status = self::STATUS_SKIPPED;
        $this->reason = $reason;

        return $this;
    }
}

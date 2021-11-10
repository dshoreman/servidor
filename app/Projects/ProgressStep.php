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
    public const STATUS_WORKING = 'working';
    public const STATUS_DONE = 'complete';

    private string $name;

    private string $text;

    private int $progress;

    private string $status;

    private string $reason = '';

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

    public function start(): self
    {
        $this->status = self::STATUS_WORKING;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'progress' => $this->progress,
            'reason' => $this->reason,
            'status' => $this->status,
            'text' => $this->text,
        ];
    }
}

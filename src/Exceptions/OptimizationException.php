<?php

namespace CoderDen\ImageOptimizer\Exceptions;

class OptimizationException extends \Exception
{
    protected string $command;
    protected array $output;

    public function __construct(string $message, string $command = '', array $output = [], int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->command = $command;
        $this->output = $output;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getOutput(): array
    {
        return $this->output;
    }
}
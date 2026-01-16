<?php

namespace CoderDen\ImageOptimizer\Optimizers;

use CoderDen\ImageOptimizer\Contracts\OptimizerInterface;
use CoderDen\ImageOptimizer\Exceptions\OptimizationException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

abstract class BaseOptimizer implements OptimizerInterface
{
    protected string $binaryPath;
    protected array $options = [];
    protected string $sourcePath;
    protected int $originalSize = 0;
    protected int $optimizedSize = 0;
    protected bool $overwrite = true;

    public function __construct(?string $binaryPath = null)
    {
        $this->binaryPath = $binaryPath ?: $this->getDefaultBinaryPath();
        $this->validateBinary();
    }

    abstract protected function getDefaultBinaryPath(): string;
    abstract protected function getCommand(string $source, string $destination): array;

    public function optimize(string $sourcePath, ?string $destinationPath = null): bool
    {
        $this->sourcePath = $sourcePath;
        $this->originalSize = filesize($sourcePath);
        
        $destination = $destinationPath ?: ($this->overwrite ? $sourcePath : tempnam(sys_get_temp_dir(), 'opt_'));
        
        try {
            $command = $this->getCommand($sourcePath, $destination);
            $process = new Process($command);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $this->optimizedSize = filesize($destination);
            
            if ($destination !== $sourcePath && $this->overwrite) {
                rename($destination, $sourcePath);
            }

            return true;

        } catch (ProcessFailedException $e) {
            throw new OptimizationException(
                sprintf('Optimization failed: %s', $e->getMessage()),
                implode(' ', $command),
                $process->getOutput() ? explode("\n", $process->getOutput()) : [],
                $e->getCode(),
                $e
            );
        }
    }

    public function setOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function getOptimizedSize(): int
    {
        return $this->optimizedSize ?: $this->originalSize;
    }

    public function getCompressionRatio(): float
    {
        if ($this->originalSize === 0) {
            return 0.0;
        }
        
        return 1 - ($this->optimizedSize / $this->originalSize);
    }

    protected function validateBinary(): void
    {
        if (!file_exists($this->binaryPath) || !is_executable($this->binaryPath)) {
            throw new OptimizationException(
                sprintf('Binary not found or not executable: %s', $this->binaryPath)
            );
        }
    }

    public function setOverwrite(bool $overwrite): self
    {
        $this->overwrite = $overwrite;
        return $this;
    }
}
<?php

namespace CoderDen\ImageOptimizer;

class OptimizationResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $sourcePath,
        public readonly ?string $destinationPath,
        public readonly int $originalSize,
        public readonly int $optimizedSize,
        public readonly float $compressionRatio,
        public readonly ?string $optimizerClass,
        public readonly ?string $error = null
    ) {}

    public function getSavedBytes(): int
    {
        return $this->originalSize - $this->optimizedSize;
    }

    public function getCompressionPercentage(): float
    {
        return $this->compressionRatio * 100;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'source_path' => $this->sourcePath,
            'destination_path' => $this->destinationPath,
            'original_size' => $this->originalSize,
            'optimized_size' => $this->optimizedSize,
            'saved_bytes' => $this->getSavedBytes(),
            'compression_ratio' => $this->compressionRatio,
            'compression_percentage' => $this->getCompressionPercentage(),
            'optimizer_class' => $this->optimizerClass,
            'error' => $this->error,
        ];
    }
}
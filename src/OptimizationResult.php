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

    public function getSavedKilobytes(): float
    {
        return $this->getSavedBytes() / 1024;
    }

    public function getSavedMegabytes(): float
    {
        return $this->getSavedBytes() / (1024 * 1024);
    }

    public function getCompressionPercentage(): float
    {
        return $this->compressionRatio * 100;
    }

    public function getFormattedOriginalSize(): string
    {
        return $this->formatBytes($this->originalSize);
    }

    public function getFormattedOptimizedSize(): string
    {
        return $this->formatBytes($this->optimizedSize);
    }

    public function getFormattedSavedSize(): string
    {
        return $this->formatBytes($this->getSavedBytes());
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'source_path' => $this->sourcePath,
            'destination_path' => $this->destinationPath,
            'original_size' => $this->originalSize,
            'original_size_formatted' => $this->getFormattedOriginalSize(),
            'optimized_size' => $this->optimizedSize,
            'optimized_size_formatted' => $this->getFormattedOptimizedSize(),
            'saved_bytes' => $this->getSavedBytes(),
            'saved_kilobytes' => $this->getSavedKilobytes(),
            'saved_megabytes' => $this->getSavedMegabytes(),
            'saved_formatted' => $this->getFormattedSavedSize(),
            'compression_ratio' => $this->compressionRatio,
            'compression_percentage' => $this->getCompressionPercentage(),
            'optimizer_class' => $this->optimizerClass,
            'error' => $this->error,
        ];
    }
}
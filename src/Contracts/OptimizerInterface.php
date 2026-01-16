<?php

namespace CoderDen\ImageOptimizer\Contracts;

interface OptimizerInterface
{
    public function optimize(string $sourcePath, ?string $destinationPath = null): bool;
    public function getOptimizedSize(): int;
    public function getCompressionRatio(): float;
}
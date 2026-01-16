<?php

namespace CoderDen\ImageOptimizer;

use CoderDen\ImageOptimizer\Contracts\OptimizerInterface;
use CoderDen\ImageOptimizer\Optimizers\JpegOptimizer;
use CoderDen\ImageOptimizer\Optimizers\PngOptimizer;
use CoderDen\ImageOptimizer\Exceptions\OptimizationException;

class ImageOptimizer
{
    protected array $optimizers = [];
    protected array $defaultConfig = [
        'jpeg' => [
            'quality' => 85,
            'strip_all' => true,
            'progressive' => true,
        ],
        'png' => [
            'optimizer' => 'pngquant',
            'quality' => 80,
            'speed' => 3,
        ],
    ];

    public function __construct(array $config = [])
    {
        $this->defaultConfig = array_merge($this->defaultConfig, $config);
    }

    public function optimize(string $imagePath, ?string $destinationPath = null, array $options = []): OptimizationResult
    {
        $optimizer = $this->getOptimizerForImage($imagePath);
        
        if (!empty($options)) {
            $this->applyOptions($optimizer, $options);
        }

        $originalSize = filesize($imagePath);
        $success = $optimizer->optimize($imagePath, $destinationPath);
        $optimizedSize = $optimizer->getOptimizedSize();

        return new OptimizationResult(
            $success,
            $imagePath,
            $destinationPath,
            $originalSize,
            $optimizedSize,
            $optimizer->getCompressionRatio(),
            get_class($optimizer)
        );
    }

    protected function getOptimizerForImage(string $imagePath): OptimizerInterface
    {
        $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        $mimeType = mime_content_type($imagePath);

        return match(true) {
            in_array($extension, ['jpg', 'jpeg']) || str_contains($mimeType, 'jpeg') => 
                $this->createJpegOptimizer(),
            $extension === 'png' || str_contains($mimeType, 'png') => 
                $this->createPngOptimizer(),
            default => throw new OptimizationException(
                sprintf('Unsupported image type: %s (%s)', $extension, $mimeType)
            ),
        };
    }

    protected function createJpegOptimizer(): JpegOptimizer
    {
        $optimizer = new JpegOptimizer();
        $config = $this->defaultConfig['jpeg'] ?? [];
        
        $optimizer
            ->setQuality($config['quality'] ?? 85)
            ->setStripAll($config['strip_all'] ?? true)
            ->setProgressive($config['progressive'] ?? true);

        if (isset($config['options']) && is_array($config['options'])) {
            $optimizer->setOptions($config['options']);
        }

        return $optimizer;
    }

    protected function createPngOptimizer(): PngOptimizer
    {
        $optimizer = new PngOptimizer();
        $config = $this->defaultConfig['png'] ?? [];
        
        $optimizer
            ->setOptimizer($config['optimizer'] ?? 'pngquant')
            ->setQuality($config['quality'] ?? 80)
            ->setSpeed($config['speed'] ?? 3);

        if (isset($config['options']) && is_array($config['options'])) {
            $optimizer->setOptions($config['options']);
        }

        return $optimizer;
    }

    protected function applyOptions(OptimizerInterface $optimizer, array $options): void
    {
        if ($optimizer instanceof JpegOptimizer) {
            foreach (['quality', 'stripAll', 'progressive'] as $method) {
                if (isset($options[$method])) {
                    $setter = 'set' . ucfirst($method);
                    $optimizer->$setter($options[$method]);
                }
            }
        } elseif ($optimizer instanceof PngOptimizer) {
            foreach (['quality', 'speed', 'optimizer'] as $method) {
                if (isset($options[$method])) {
                    $setter = 'set' . ucfirst($method);
                    $optimizer->$setter($options[$method]);
                }
            }
        }
    }

    public function batchOptimize(array $imagePaths, array $options = []): array
    {
        $results = [];
        
        foreach ($imagePaths as $imagePath) {
            try {
                $results[] = $this->optimize($imagePath, null, $options);
            } catch (OptimizationException $e) {
                $results[] = new OptimizationResult(
                    false,
                    $imagePath,
                    null,
                    filesize($imagePath),
                    filesize($imagePath),
                    0.0,
                    null,
                    $e->getMessage()
                );
            }
        }
        
        return $results;
    }
}
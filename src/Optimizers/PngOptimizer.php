<?php

namespace CoderDen\ImageOptimizer\Optimizers;

class PngOptimizer extends BaseOptimizer
{
    protected string $optimizer = 'pngquant';
    protected int $quality = 80;
    protected int $speed = 3;

    protected function getDefaultBinaryPath(): string
    {
        return match($this->optimizer) {
            'pngquant' => '/usr/bin/pngquant',
            'optipng' => '/usr/bin/optipng',
            'pngcrush' => '/usr/bin/pngcrush',
            default => '/usr/bin/pngquant'
        };
    }

    protected function getCommand(string $source, string $destination): array
    {
        return match($this->optimizer) {
            'pngquant' => $this->getPngquantCommand($source, $destination),
            'optipng' => $this->getOptipngCommand($source, $destination),
            'pngcrush' => $this->getPngcrushCommand($source, $destination),
        };
    }

    private function getPngquantCommand(string $source, string $destination): array
    {
        $command = [$this->binaryPath];
        
        if ($this->quality) {
            $command[] = '--quality=' . $this->quality;
        }
        
        if ($this->speed) {
            $command[] = '--speed=' . $this->speed;
        }
        
        foreach ($this->options as $option => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $command[] = '--' . $option;
                }
            } else {
                $command[] = '--' . $option . '=' . $value;
            }
        }
        
        $command[] = '--output=' . $destination;
        $command[] = $source;
        
        return $command;
    }

    private function getOptipngCommand(string $source, string $destination): array
    {
        $command = [$this->binaryPath, '-o2', '-strip', 'all'];
        
        foreach ($this->options as $option => $value) {
            $command[] = '-' . $option . $value;
        }
        
        $command[] = '-out=' . $destination;
        $command[] = $source;
        
        return $command;
    }

    private function getPngcrushCommand(string $source, string $destination): array
    {
        $command = [$this->binaryPath];
        
        foreach ($this->options as $option => $value) {
            $command[] = '-' . $option . $value;
        }
        
        $command[] = $source;
        $command[] = $destination;
        
        return $command;
    }

    public function setOptimizer(string $optimizer): self
    {
        $this->optimizer = $optimizer;
        $this->binaryPath = $this->getDefaultBinaryPath();
        return $this;
    }

    public function setQuality(int $quality): self
    {
        $this->quality = max(0, min(100, $quality));
        return $this;
    }

    public function setSpeed(int $speed): self
    {
        $this->speed = max(1, min(10, $speed));
        return $this;
    }
}
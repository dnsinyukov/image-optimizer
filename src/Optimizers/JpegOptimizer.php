<?php

namespace CoderDen\ImageOptimizer\Optimizers;

class JpegOptimizer extends BaseOptimizer
{
    protected int $quality = 85;
    protected bool $stripAll = true;
    protected bool $progressive = true;

    protected function getDefaultBinaryPath(): string
    {
        return '/usr/bin/jpegoptim';
    }

    protected function getCommand(string $source, string $destination): array
    {
        $command = [$this->binaryPath];

        if ($this->quality) {
            $command[] = '--max=' . $this->quality;
        }

        if ($this->stripAll) {
            $command[] = '--strip-all';
        }

        if ($this->progressive) {
            $command[] = '--all-progressive';
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

        $command[] = $source;
        
        if (!$this->overwrite && $source !== $destination) {
            $command[] = '--dest=' . dirname($destination);
        }

        return $command;
    }

    public function setQuality(int $quality): self
    {
        $this->quality = max(0, min(100, $quality));
        return $this;
    }

    public function setStripAll(bool $stripAll): self
    {
        $this->stripAll = $stripAll;
        return $this;
    }

    public function setProgressive(bool $progressive): self
    {
        $this->progressive = $progressive;
        return $this;
    }
}
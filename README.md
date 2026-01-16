# Image Optimizer for PHP

A powerful PHP package for optimizing images using external tools like jpegoptim, pngquant, optipng, and pngcrush. Perfect for Laravel applications but can be used in any PHP project.

## Features

- 🚀 **Multiple optimizers**: Support for jpegoptim, pngquant, optipng, and pngcrush
- 📊 **Detailed metrics**: Get compression ratio, saved bytes, and optimization statistics
- ⚙️ **Flexible configuration**: Per-image type settings with quality and optimization options
- 🔄 **Batch processing**: Optimize multiple images at once
- 🛡️ **Error handling**: Comprehensive exception system with detailed error information
- 📈 **Performance**: Optimizes images without quality loss (or configurable loss)

## Requirements

- PHP 8.1 or higher
- External optimization tools installed:
  - `jpegoptim` for JPEG images
  - `pngquant`, `optipng`, or `pngcrush` for PNG images

### Install Required Tools

#### Ubuntu/Debian:
```bash
sudo apt-get install jpegoptim pngquant optipng pngcrush
```

#### macOS (Homebrew):
```bash
brew install jpegoptim pngquant optipng pngcrush
```

#### CentOS/RHEL:
```bash
sudo yum install jpegoptim pngquant optipng pngcrush
```

## Installation

### For Standalone PHP Projects

```bash
composer require coderden/image-optimizer
```

## Usage

### Basic Usage

```php
// Create optimizer instance with default configuration
$optimizer = new ImageOptimizer();

// Optimize a single image
$result = $optimizer->optimize('/path/to/image.jpg');

// Get optimization results
echo "Optimization successful: " . ($result->success ? 'Yes' : 'No') . "\n";
echo "Saved: " . $result->getSavedBytes() . " bytes\n";
echo "Compression: " . $result->getCompressionPercentage() . "%\n";
echo "Original size: " . $result->originalSize . " bytes\n";
echo "Optimized size: " . $result->optimizedSize . " bytes\n";
```

### With Custom Options

```php
$optimizer = new ImageOptimizer();

// Optimize with custom settings
$result = $optimizer->optimize(
    '/path/to/image.jpg',
    '/path/to/output.jpg', // Optional destination path
    [
        'quality' => 75,
        'stripAll' => true,
        'progressive' => false
    ]
);

// For PNG images
$result = $optimizer->optimize(
    '/path/to/image.png',
    null, // Overwrite original
    [
        'optimizer' => 'optipng',
        'quality' => 90,
        'speed' => 5
    ]
);
```

### Batch Optimization

```php
$optimizer = new ImageOptimizer();

$results = $optimizer->batchOptimize([
    '/path/to/image1.jpg',
    '/path/to/image2.png',
    '/path/to/image3.jpg',
]);

foreach ($results as $result) {
    if ($result->success) {
        echo "Optimized {$result->sourcePath}: saved {$result->getSavedBytes()} bytes\n";
    } else {
        echo "Failed to optimize {$result->sourcePath}: {$result->error}\n";
    }
}
```

### Custom Configuration

```php
$config = [
    'jpeg' => [
        'quality' => 70,
        'progressive' => false,
        'strip_all' => false,
    ],
    'png' => [
        'optimizer' => 'optipng',
        'quality' => 90,
        'speed' => 1, // Slowest but best compression
    ],
];

$optimizer = new ImageOptimizer($config);
```

## Optimizers

### JPEG Optimizer (jpegoptim)
- **Quality**: 0-100 (default: 85)
- **Strip All**: Remove all metadata (default: true)
- **Progressive**: Create progressive JPEGs (default: true)

### PNG Optimizers
You can choose between three optimizers:

1. **pngquant** (default): Best for quality/size ratio
   - Quality: 0-100 (default: 80)
   - Speed: 1-10 (1=slow/best, 10=fast/good)

2. **optipng**: Lossless optimization
   - Uses various optimization levels

3. **pngcrush**: Alternative lossless optimizer

## Error Handling

The package throws `OptimizationException` when something goes wrong:

```php
try {
    $optimizer = new ImageOptimizer();
    $result = $optimizer->optimize('/path/to/image.jpg');
} catch (OptimizationException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Command: " . $e->getCommand() . "\n";
    echo "Output: " . implode("\n", $e->getOutput()) . "\n";
}
```
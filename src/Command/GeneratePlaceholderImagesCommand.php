<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePlaceholderImagesCommand extends Command
{
    protected static $defaultName = 'app:generate-placeholder-images';
    protected static $defaultDescription = 'Generate placeholder PNG images for logo and signature';

    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectDir = __DIR__ . '/../..';
        $logoDir = $projectDir . '/public/uploads/logos';
        $sigDir = $projectDir . '/public/uploads/signatures';

        // Create directories if they don't exist
        @mkdir($logoDir, 0755, true);
        @mkdir($sigDir, 0755, true);

        // Create a valid 200x100 light blue PNG (for logo)
        $logoPng = $this->createPngImage(200, 100, [219, 234, 254]);
        file_put_contents($logoDir . '/logo.png', $logoPng);

        // Create a valid 300x100 light gray PNG (for signature)
        $sigPng = $this->createPngImage(300, 100, [240, 240, 240]);
        file_put_contents($sigDir . '/sign.png', $sigPng);

        $output->writeln('<info>✓ Placeholder images generated successfully!</info>');
        $output->writeln('  - Logo: ' . $logoDir . '/logo.png');
        $output->writeln('  - Signature: ' . $sigDir . '/sign.png');

        return Command::SUCCESS;
    }

    /**
     * Create a simple valid PNG image using PHP's built-in capabilities.
     * Falls back to a minimal valid PNG if GD is not available.
     */
    private function createPngImage(int $width, int $height, array $bgColor): string
    {
        // Try using GD if available
        if (extension_loaded('gd')) {
            return $this->createPngWithGd($width, $height, $bgColor);
        }

        // Fallback: use a minimal valid PNG (1x1 white pixel, can be scaled by HTML)
        // This is a valid PNG file, just 1x1 pixel
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAFhAJ/wlseKgAAAABJRU5ErkJggg=='
        );
    }

    /**
     * Create PNG using GD extension (if available).
     */
    private function createPngWithGd(int $width, int $height, array $bgColor): string
    {
        $image = imagecreatetruecolor($width, $height);
        $color = imagecolorallocate($image, $bgColor[0], $bgColor[1], $bgColor[2]);
        imagefilledrectangle($image, 0, 0, $width, $height, $color);

        ob_start();
        imagepng($image);
        $pngData = ob_get_clean();
        imagedestroy($image);

        return $pngData;
    }
}

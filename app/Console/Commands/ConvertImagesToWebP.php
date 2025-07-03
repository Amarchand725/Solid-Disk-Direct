<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class ConvertImagesToWebP extends Command
{
    protected $signature = 'images:convert-webp {--quality=85}';
    // protected $description = 'Convert images to WebP format in app/public/uploads/products';
    // protected $description = 'Convert images to WebP format in app/public/uploads/sliders';
    protected $description = 'Convert images to WebP format in app/public/uploads/banners';
    
    public function handle()
    {
        $quality = (int) $this->option('quality');
        $manager = new ImageManager(new Driver());

        // $sourceDir = storage_path('app/public/uploads/banners');
        // $targetDir = storage_path('app/public/uploads/banners-webp');
        
        // $sourceDir = storage_path('app/public/uploads/sliders');
        // $targetDir = storage_path('app/public/uploads/sliders-webp');
        
        $sourceDir = storage_path('app/public/uploads/products');
        $targetDir = storage_path('app/public/uploads/products-webp');

        if (!File::exists($sourceDir)) {
            $this->error("Source directory not found: $sourceDir");
            return;
        }

        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $imageFiles = File::allFiles($sourceDir);
        foreach ($imageFiles as $file) {
            $path = $file->getPathname();
            $extension = strtolower($file->getExtension());

            if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                continue;
            }

            try {
                $image = $manager->read($path);

                $relativePath = str_replace($sourceDir, '', $path);
                $webpPath = $targetDir . DIRECTORY_SEPARATOR . preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', ltrim($relativePath, DIRECTORY_SEPARATOR));

                // Ensure subdirectories are created
                File::ensureDirectoryExists(dirname($webpPath));

                // Avoid overwriting existing WebP files
                if (File::exists($webpPath)) {
                    $this->line("Skipped (already exists): " . str_replace(base_path(), '', $webpPath));
                    continue;
                }

                // Convert and save as webp
                $image->toWebp()->save($webpPath, $quality);
                $this->info("Converted: " . str_replace(base_path(), '', $webpPath));

                // ✅ Delete the original after successful conversion
                if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    File::delete($path);
                    $this->line("Deleted original: " . str_replace(base_path(), '', $path));
                }

            } catch (\Exception $e) {
                $this->error("Failed to convert: $path. Error: " . $e->getMessage());
            }
        }


        // foreach ($imageFiles as $file) {
        //     $path = $file->getPathname();
        //     $extension = strtolower($file->getExtension());

        //     if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
        //         continue;
        //     }

        //     try {
        //         $image = $manager->read($path);

        //         $relativePath = str_replace($sourceDir, '', $path);
        //         $webpPath = $targetDir . DIRECTORY_SEPARATOR . preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', ltrim($relativePath, DIRECTORY_SEPARATOR));

        //         // Ensure subdirectories are created
        //         File::ensureDirectoryExists(dirname($webpPath));

        //         $image->toWebp()->save($webpPath, $quality);
        //         $this->info("Converted: " . str_replace(base_path(), '', $webpPath));
        //     } catch (\Exception $e) {
        //         $this->error("Failed to convert: $path. Error: " . $e->getMessage());
        //     }
        // }

        $this->info("✅ WebP conversion complete.");
    }
}
<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Feed;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

/**
 * Writes the formatted feed body to the public media directory atomically.
 *
 * Layout: pub/media/llmo/feed-{exporter}-{storeCode}.{ext}
 */
class FeedWriter
{
    public const SUBDIR = 'llmo';

    // phpcs:ignore
    public function __construct(
        private readonly Filesystem $filesystem
    ) {
    }

    public function write(string $exporterCode, string $storeCode, string $extension, string $body): string
    {
        $directory = $this->mediaDirectory();
        $directory->create(self::SUBDIR);

        $relativeFinal = $this->relativePath($exporterCode, $storeCode, $extension);
        $relativeTemp  = $relativeFinal . '.tmp';

        $directory->writeFile($relativeTemp, $body);
        $directory->renameFile($relativeTemp, $relativeFinal);

        return $directory->getAbsolutePath($relativeFinal);
    }

    public function absolutePath(string $exporterCode, string $storeCode, string $extension): string
    {
        return $this->mediaDirectory()->getAbsolutePath(
            $this->relativePath($exporterCode, $storeCode, $extension)
        );
    }

    public function exists(string $exporterCode, string $storeCode, string $extension): bool
    {
        return $this->mediaDirectory()->isExist(
            $this->relativePath($exporterCode, $storeCode, $extension)
        );
    }

    public function read(string $exporterCode, string $storeCode, string $extension): string
    {
        return $this->mediaDirectory()->readFile(
            $this->relativePath($exporterCode, $storeCode, $extension)
        );
    }

    private function relativePath(string $exporterCode, string $storeCode, string $extension): string
    {
        $code = \preg_replace('/[^a-z0-9_\-]/i', '', $exporterCode) ?: 'feed';
        $store = \preg_replace('/[^a-z0-9_\-]/i', '', $storeCode) ?: 'default';
        $ext = \preg_replace('/[^a-z0-9]/i', '', $extension) ?: 'json';

        return self::SUBDIR . '/feed-' . $code . '-' . $store . '.' . $ext;
    }

    private function mediaDirectory(): WriteInterface
    {
        return $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }
}

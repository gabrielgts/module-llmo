<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Feed;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

/**
 * Writes the formatted feed body to the public media directory atomically.
 *
 * Layout: pub/media/llmo/feed-{exporter}-{storeCode}.{ext}
 *
 * Both `$exporterCode` and `$storeCode` must contain at least one character
 * that survives the alphanumeric/dash/underscore sanitisation. An empty result
 * after sanitisation throws `\InvalidArgumentException` so callers never
 * silently overwrite a different exporter's file.
 */
class FeedWriter
{
    public const SUBDIR = 'llmo';

    /**
     * @param Filesystem $filesystem
     */
    // phpcs:ignore
    public function __construct(
        private readonly Filesystem $filesystem
    ) {
    }

    /**
     * Write the feed body to disk atomically (temp-rename pattern).
     *
     * @param string $exporterCode
     * @param string $storeCode
     * @param string $extension
     * @param string $body
     * @return string Absolute path of the written file.
     * @throws \InvalidArgumentException When exporter code or store code reduce to an empty string.
     * @throws FileSystemException       On write or rename failure.
     */
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

    /**
     * Return the absolute filesystem path for a feed file (may not exist yet).
     *
     * @param string $exporterCode
     * @param string $storeCode
     * @param string $extension
     * @return string
     * @throws \InvalidArgumentException When exporter code or store code reduce to an empty string.
     */
    public function absolutePath(string $exporterCode, string $storeCode, string $extension): string
    {
        return $this->mediaDirectory()->getAbsolutePath(
            $this->relativePath($exporterCode, $storeCode, $extension)
        );
    }

    /**
     * Check whether the feed file exists on disk.
     *
     * @param string $exporterCode
     * @param string $storeCode
     * @param string $extension
     * @return bool
     * @throws \InvalidArgumentException When exporter code or store code reduce to an empty string.
     */
    public function exists(string $exporterCode, string $storeCode, string $extension): bool
    {
        return $this->mediaDirectory()->isExist(
            $this->relativePath($exporterCode, $storeCode, $extension)
        );
    }

    /**
     * Read and return the raw feed body from disk.
     *
     * @param string $exporterCode
     * @param string $storeCode
     * @param string $extension
     * @return string
     * @throws \InvalidArgumentException When exporter code or store code reduce to an empty string.
     * @throws FileSystemException       When the file does not exist or cannot be read.
     */
    public function read(string $exporterCode, string $storeCode, string $extension): string
    {
        return $this->mediaDirectory()->readFile(
            $this->relativePath($exporterCode, $storeCode, $extension)
        );
    }

    /**
     * Build the media-relative path for a feed file.
     *
     * Strips every character that is not alphanumeric, dash, or underscore from
     * both `$exporterCode` and `$storeCode`. Throws when either reduces to an
     * empty string to prevent silent file-name collisions between exporters.
     *
     * @param string $exporterCode
     * @param string $storeCode
     * @param string $extension
     * @return string
     * @throws \InvalidArgumentException
     */
    private function relativePath(string $exporterCode, string $storeCode, string $extension): string
    {
        $code = (string) \preg_replace('/[^a-z0-9_\-]/i', '', $exporterCode);
        if ($code === '') {
            throw new \InvalidArgumentException(
                \sprintf('Exporter code "%s" produces an empty filename segment.', $exporterCode)
            );
        }

        $store = (string) \preg_replace('/[^a-z0-9_\-]/i', '', $storeCode);
        if ($store === '') {
            throw new \InvalidArgumentException(
                \sprintf('Store code "%s" produces an empty filename segment.', $storeCode)
            );
        }

        $ext = (string) \preg_replace('/[^a-z0-9]/i', '', $extension) ?: 'json';

        return self::SUBDIR . '/feed-' . $code . '-' . $store . '.' . $ext;
    }

    /**
     * Return a writable handle to the Magento media directory.
     *
     * @return WriteInterface
     */
    private function mediaDirectory(): WriteInterface
    {
        return $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }
}

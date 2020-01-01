<?php

declare(strict_types=1);

/*
 * This file is part of Class Preloader.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 * (c) Michael Dowling <mtdowling@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClassPreloader;

use RuntimeException;

/**
 * This is the output writer.
 */
final class OutputWriter
{
    /**
     * Open an output file, ensuring its directory exists.
     *
     * @param string $filePath
     *
     * @throws \RuntimeException
     *
     * @return resource
     */
    public static function openOutputFile(string $filePath)
    {
        $dirPath = dirname($filePath);

        if (!FileUtils::ensureDirectoryExists($dirPath)) {
            throw new RuntimeException("Unable to create directory $dirPath.");
        }

        $handle = FileUtils::openFileForWriting($filePath);

        if ($handle === false) {
            throw new RuntimeException("Unable to open $filePath for writing.");
        }

        return $handle;
    }

    /**
     * Write an opening PHP tag to the given handle.
     *
     * @param resource $handle
     * @param bool     $strictTypes
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public static function writeOpeningTag($handle, bool $strictTypes)
    {
        if (!FileUtils::writeString($handle, $strictTypes ? "<?php declare(strict_types=1);\n" : "<?php\n")) {
            throw new RuntimeException('Unable to write opening tag to the output file.');
        }
    }

    /**
     * Write the given file content to the given handle.
     *
     * @param resource $handle
     * @param string   $fileContent
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public static function writeFileContent($handle, string $fileContent)
    {
        if (!FileUtils::writeString($handle, $fileContent)) {
            throw new RuntimeException('Unable to write file content to the output file.');
        }
    }

    /**
     * Close the given handle.
     *
     * @param resource $handle
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public static function closeHandle($handle)
    {
        if (!FileUtils::closeHandle($handle)) {
            throw new RuntimeException('Unable to close the output file.');
        }
    }
}

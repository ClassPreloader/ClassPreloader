<?php

/*
 * This file is part of Class Preloader.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 * (c) Michael Dowling <mtdowling@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClassPreloader\Console;

use ClassPreloader\Config;
use InvalidArgumentException;

/**
 * This is the config resolver class.
 */
class ConfigResolver
{
    /**
     * Get a list of files in order.
     *
     * @param mixed $config
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getFileList($config)
    {
        if (strpos($config, ',')) {
            return array_filter(explode(',', $config));
        }

        if (!$this->isAbsolutePath($config)) {
            $config = getcwd().'/'.$config;
        }

        // Ensure that the config file exists
        if (!file_exists($config)) {
            throw new InvalidArgumentException(sprintf('Configuration file "%s" does not exist.', $config));
        }

        $result = require $config;

        if ($result instanceof Config) {
            return $result->getFilenames();
        }

        if (is_array($result)) {
            return $result;
        }

        throw new InvalidArgumentException('Config must return an array of filenames or a Config object.');
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @param string $file
     *
     * @return bool
     */
    protected function isAbsolutePath($file)
    {
        return (strspn($file, '/\\', 0, 1)
            || (strlen($file) > 3 && ctype_alpha($file[0])
                && substr($file, 1, 1) === ':'
                && (strspn($file, '/\\', 2, 1))
            )
            || null !== parse_url($file, PHP_URL_SCHEME)
        );
    }
}

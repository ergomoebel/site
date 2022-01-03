<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Test\Integration\FileResolver\Sftp;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class FakeSftp extends \Magento\Framework\Filesystem\Io\Sftp
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $files;

    private $cwd = '/';

    private $opened = false;

    public function __construct(
        string $host,
        string $username,
        string $password,
        array $files
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->files = $files;
    }

    public function open(array $args = [])
    {
        foreach (['host', 'username', 'password'] as $field) {
            if (!isset($args[$field]) || $this->{$field} != $args[$field]) {
                throw new LocalizedException(new Phrase('Bad credentials or host info'));
            }
        }

        $this->opened = true;
    }

    public function cd($dir)
    {
        $this->cwd = $dir;
    }

    public function ls($grep = null)
    {
        if (!$this->opened) {
            throw new \RuntimeException('Connection is not established');
        }
        $result = [];
        foreach ($this->files as $path => $contents) {
            if (strpos($path, $this->cwd) === 0) {
                $result [] = [
                    'text' => basename($path),
                    'id'   => $path
                ];
            }
        }

        return $result;
    }

    public function read($filename, $dest = null)
    {
        if (!$this->opened) {
            throw new \RuntimeException('Connection is not established');
        }
        if (!is_string($dest)) {
            throw new \RuntimeException('Unsupported destination type in test FTP class. Only file path can be used');
        }
        $fullName = rtrim($this->cwd, '/') . '/' . $filename;
        if (isset($this->files[$fullName])) {
            return (bool)file_put_contents($dest, $this->files[$fullName]);
        } else {
            throw new \RuntimeException('File does not exist: ' . $fullName);
        }
    }

    public function close()
    {
        $this->opened = false;
    }

    public function isOpened(): bool
    {
        return $this->opened;
    }
}

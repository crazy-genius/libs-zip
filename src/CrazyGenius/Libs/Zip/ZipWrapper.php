<?php

declare(strict_types=1);

namespace CrazyGenius\Libs\Zip;

class ZipWrapper
{
    public function __construct(
        private string $filePath,
    ) {}

    /**
     * @param string $destination
     */
    public function extractAll(string $destination): void
    {
        $arch = new  \ZipArchive();
        if (!$arch->open($this->filePath)) {
            throw new \RuntimeException("Couldn't opn archive");
        }

        if (!$arch->extractTo($destination)) {
            throw new \RuntimeException("Couldn't unzip to $destination");
        }

        if (!$arch->close()) {
            throw new \RuntimeException("Couldn't close archive");
        }
    }


    /**
     * @param string $file
     * @param string|resource $destination
     */
    public function extractFile(string $file, $destination): void
    {
        if (is_resource($destination)) {
            $readStream = fopen($this->getStreamPath() . "#$file", 'r+b');

            if (!stream_copy_to_stream($readStream, $destination)) {
                throw new \RuntimeException("Couldn't extract file $file");
            }

            fclose($readStream);
        } elseif (is_string($destination)) {
            if (!copy($this->getStreamPath() . "#$file", $destination)) {
                throw new \RuntimeException("Couldn't extract file $file to $destination");
            }
        } else {
            throw new \InvalidArgumentException('Unsupported destination');
        }
    }

    private function getStreamPath(): string
    {
        return 'zip://' . $this->filePath;
    }
}


<?php

namespace App\Models\Domain\FileParsers;

interface FileParser {
    /**
     * Parses a file and returns its contents as an array.
     *
     * @param string $filename
     * @return array
     */
    public function parse(string $filename): array;
}

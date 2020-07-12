<?php


namespace App\Reader;

/**
 * Interface FileReaderInterface
 * @package App\Reader
 */
interface FileReaderInterface
{
    /**
     * @param string $file
     * @return array
     * @throws \Exception
     */
    public function getData(string $file);
}

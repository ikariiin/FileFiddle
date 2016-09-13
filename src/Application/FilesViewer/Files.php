<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 12/9/16
 * Time: 4:11 PM
 */

namespace FileFiddle\Application\FilesViewer;
class Files {
    private $fileName;

    public function __construct(string $fileName) {
        if(!is_file($fileName) || is_dir($fileName)) {
            throw new \RuntimeException("The file name must be a valid absolute file name present on the disc. Which it is not clearly af.");
        }
        $this->fileName = $fileName;
    }

    public function getJsonReadyData(): array {
        $fileName = $this->fileName;
        $fileContent = file_get_contents($fileName);
        $splFile = new \SplFileInfo($fileName);
        return [
            "writable" => $splFile->isWritable(),
            "lastModified" => date("H:i:s d M Y", $splFile->getMTime()),
            "createdAt" => date("H:i:s d M Y", $splFile->getCTime()),
            "content" => $fileContent,
            "size" => ceil($splFile->getSize() / 1024),
            "extension" => $splFile->getExtension()
        ];
    }
}
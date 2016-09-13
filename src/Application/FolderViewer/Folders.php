<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 11/9/16
 * Time: 7:15 PM
 */

namespace FileFiddle\Application\FolderViewer;
class Folders {
    private $fileSystemIterator;

    public function __construct(string $dir) {
        if(!is_dir($dir)) {
            throw new \RuntimeException("The directory name provided must be a valid directory. Which it is clearly not.");
        }
        $fileSystemIterator = new \FilesystemIterator($dir);
        $this->fileSystemIterator = $fileSystemIterator;
    }

    public function getJsonReadyList(): array {
        $data = [];
        foreach($this->fileSystemIterator as $file) {
            $data[] = [
                "name" => $file->getFilename(),
                "lastModified" => date("H:i:s", $file->getMTime()),
                "type" => ($file->isDir()) ? "dir" : "file",
                "path" => $file->getPathname(),
                "size" => ceil($file->getSize() / 1024)
            ];
        }
        return $data;
    }
}

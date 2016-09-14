<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 13/9/16
 * Time: 10:24 PM
 */

namespace FileFiddle\Application\Terminal;
class Main {
    private $command;

    public function __construct(string $command) {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getCommand() {
        return $this->command;
    }

    public function executeCommand(): string {
        return (new Executor($this->command))
            ->run()
            ->getOutput();
    }
}
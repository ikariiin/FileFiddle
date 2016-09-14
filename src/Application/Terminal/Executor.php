<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 13/9/16
 * Time: 10:28 PM
 */

namespace FileFiddle\Application\Terminal;
class Executor {
    private $command;
    /**
     * @var string
     */
    private $output;

    public function __construct(string $command) {
        $this->command = $command . " 2>&1";
    }

    public function run(): self {
        $output = shell_exec($this->command);
        $this->output = $output ?? "";
        $this->output = trim($this->output);
        return $this;
    }

    public function getOutput() {
        return $this->output;
    }
}
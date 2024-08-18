<?php

namespace Src;

use League\CLImate\CLImate;

class ProgressBar
{
    private $progressBar;
    private int $currentValue = 0;
    private string $currentMessage = '';

    public function __construct(int $total) {
        $climate = new CLImate;
        $this->progressBar = $climate->progress()->total($total);
    }

    public function setCurrentValue(int $currentValue): void
    {
        $this->currentValue = $currentValue;
    }

    public function setCurrentMessage(string $currentMessage): void
    {
        $this->currentMessage = $currentMessage;
    }

    public function getCurrentMessage(): string
    {
        return $this->currentMessage;
    }

    public function show(): void
    {
        $this->progressBar->current($this->currentValue, $this->currentMessage);
    }
}

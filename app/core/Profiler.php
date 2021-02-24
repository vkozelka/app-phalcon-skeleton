<?php
namespace App\Core;

class Profiler {

    private array $timers = [];

    public function start(string $name) {
        if (isset($this->timers[$name])) {
            if (!$this->timers[$name]["duration"]) {
                throw new \Exception("Timer ".$name." is already started");
            }
        }

        $this->timers[$name] = [
            "start" => microtime(true),
            "stop" => null,
            "duration" => null
        ];
    }

    public function stop(string $name) {
        if (!isset($this->timers[$name])) {
            throw new \Exception("Timer ".$name." is not started");
        }

        $this->timers[$name]["stop"] = microtime(true);
        $this->timers[$name]["duration"] = number_format(($this->timers[$name]["stop"] - $this->timers[$name]["start"])*1000,6,"."," ");
    }

    public function getTimers(): array
    {
        foreach ($this->timers as $timerName => $timerData) {
            if (is_null($timerData["duration"])) {
                $this->stop($timerName);
            }
        }
        return $this->timers;
    }

}
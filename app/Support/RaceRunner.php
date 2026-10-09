<?php

namespace App\Support;

use App\Models\User;
use App\Models\Workshop;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class RaceRunner
{
    /**
     * @param  array<string, string>  $env  extra environment for the workers (e.g. the test database)
     * @return array{booked: int, refused: int, late: int, outputs: array<int, string>}
     */
    public static function run(Workshop $workshop, User $user, int $attempts, array $env = [], float $leadSeconds = 6.0): array
    {
        $php = (new PhpExecutableFinder)->find(false) ?: 'php';
        $at = sprintf('%.6F', microtime(true) + $leadSeconds);

        $processes = [];
        for ($i = 1; $i <= $attempts; $i++) {
            $process = new Process(
                [$php, base_path('artisan'), 'workshops:register-at', $workshop->id, $user->id, "racer{$i}@example.test", "--at={$at}"],
                base_path(),
                $env ?: null,
                null,
                $leadSeconds + 60,
            );
            $process->start();
            $processes[] = $process;
        }

        $outputs = [];
        foreach ($processes as $process) {
            $process->wait();
            $outputs[] = trim($process->getOutput() . $process->getErrorOutput());
        }

        return [
            'booked' => count(array_filter($outputs, fn ($o) => $o === 'booked')),
            'refused' => count(array_filter($outputs, fn ($o) => str_starts_with($o, 'refused'))),
            'late' => count(array_filter($outputs, fn ($o) => $o === 'late')),
            'outputs' => $outputs,
        ];
    }
}

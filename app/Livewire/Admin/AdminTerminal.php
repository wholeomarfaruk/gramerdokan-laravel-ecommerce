<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class AdminTerminal extends Component
{
 public $command = '';
    public $customCommand = '';
    public $output = '';
    public $customMode = false;

    public array $allowed = [
        'pwd',
        'whoami',
        'ls -la',
        'git pull origin main',
        'php artisan optimize:clear',
        'php artisan optimize',
        'php artisan migrate --force',
        'php artisan storage:link',
        'git remote -v',
        'git status',
        'git reset --hard',
    ];

    public function mount()
    {
        $this->customMode = request('key') ==='adminterminal';
    }

    public function run()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        $projectPath = '/home/fashionf/seldomfashion.com';

        if ($this->customMode) {
            $cmdInput = trim($this->customCommand);
        } else {
            if (!in_array($this->command, $this->allowed, true)) {
                $this->output = "Command not allowed.";
                return;
            }

            $cmdInput = $this->command;
        }

        if ($cmdInput === '') {
            $this->output = "No command given.";
            return;
        }

        $cmd = 'cd ' . escapeshellarg($projectPath) . ' && ' . $cmdInput . ' 2>&1';

        $this->output = shell_exec($cmd);
    }

    public function render()
    {
        return view('livewire.admin.admin-terminal');
    }
}

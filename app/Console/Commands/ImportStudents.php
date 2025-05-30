<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use Maatwebsite\Excel\Facades\Excel;

class ImportStudents extends Command
{
    protected $signature = 'import:students {file}';
    protected $description = 'Import students from Excel file';

    public function handle()
    {
        $file = $this->argument('file');
        $rows = Excel::toArray([], storage_path("app/{$file}"));
        foreach ($rows[0] as $row) {
            $msv = trim($row[0]);
            $fullname = trim($row[1]);
            if (!$msv || !$fullname) continue;
            Student::updateOrCreate(
                ['msv' => $msv],
                ['fullname' => $fullname]
            );
        }
        $this->info('Import thành công!');
    }
}

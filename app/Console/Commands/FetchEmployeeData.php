<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZKTecoEmployeeService;

class FetchEmployeeData extends Command
{
    protected $signature = 'zkteco:fetch-employees';
    protected $description = 'Fetch all employee data from ZKTeco devices';

    public function handle()
    {
        $this->info('🚀 Fetching employee data from ZKTeco devices...');
        
        $service = new ZKTecoEmployeeService();
        $result = $service->fetchAllEmployees();
        
        if ($result['success']) {
            $this->info("✅ Successfully fetched {$result['employees_fetched']} employees");
            $this->info("📊 Saved {$result['employees_saved']} new employees");
            
            if (!empty($result['employees'])) {
                $this->info("\n📋 Employee List:");
                $this->table(
                    ['Punch Code ID', 'Name', 'Device Type', 'Device IP'],
                    collect($result['employees'])->map(function($emp) {
                        return [
                            $emp['punch_code_id'],
                            $emp['name'],
                            $emp['device_type'],
                            $emp['device_ip']
                        ];
                    })
                );
            }
        } else {
            $this->error('❌ Failed to fetch employee data');
        }
    }
}

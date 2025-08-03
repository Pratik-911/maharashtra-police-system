<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class VerifyDutyOfficers extends BaseCommand
{
    protected $group = 'app';
    protected $name = 'verify:duty-officers';
    protected $description = 'Verify if officers are being assigned to duties';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('=== DUTY ALLOCATION VERIFICATION ===', 'yellow');
        CLI::newLine();

        // Check recent duties
        CLI::write('Recent Duties:', 'green');
        $duties = $db->query("SELECT duty_id, date, shift, point_id, created_at FROM duties ORDER BY created_at DESC LIMIT 5")->getResultArray();
        foreach ($duties as $duty) {
            CLI::write("Duty ID: {$duty['duty_id']}, Date: {$duty['date']}, Shift: {$duty['shift']}, Created: {$duty['created_at']}");
        }

        CLI::newLine();

        // Check duty_officers assignments
        CLI::write('Duty-Officer Assignments:', 'green');
        $assignments = $db->query("
            SELECT do.duty_id, do.officer_id, o.name, o.badge_no, d.date, d.shift
            FROM duty_officers do 
            JOIN officers o ON o.id = do.officer_id 
            JOIN duties d ON d.duty_id = do.duty_id 
            ORDER BY do.created_at DESC LIMIT 10
        ")->getResultArray();

        if (empty($assignments)) {
            CLI::write('NO OFFICER ASSIGNMENTS FOUND!', 'red');
        } else {
            foreach ($assignments as $assignment) {
                CLI::write("Duty {$assignment['duty_id']} ({$assignment['date']} {$assignment['shift']}) -> Officer {$assignment['officer_id']} ({$assignment['name']}, Badge: {$assignment['badge_no']})");
            }
        }

        CLI::newLine();

        // Check for duties without officers
        CLI::write('Duties without Officer Assignments:', 'green');
        $unassigned = $db->query("
            SELECT d.duty_id, d.date, d.shift, p.name as point_name
            FROM duties d 
            LEFT JOIN duty_officers do ON d.duty_id = do.duty_id 
            LEFT JOIN points p ON d.point_id = p.point_id
            WHERE do.duty_id IS NULL 
            ORDER BY d.created_at DESC
        ")->getResultArray();

        if (empty($unassigned)) {
            CLI::write('All duties have officer assignments.', 'green');
        } else {
            foreach ($unassigned as $duty) {
                CLI::write("Duty {$duty['duty_id']} ({$duty['date']} {$duty['shift']}) at {$duty['point_name']} - NO OFFICERS ASSIGNED", 'red');
            }
        }

        CLI::newLine();
        CLI::write('=== VERIFICATION COMPLETE ===', 'yellow');
    }
}

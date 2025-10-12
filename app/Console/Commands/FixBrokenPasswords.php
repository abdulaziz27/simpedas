<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class FixBrokenPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:broken-passwords {--password=password123 : Default password to set} {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix broken password hashes from TURBO import that created invalid password hashes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $defaultPassword = $this->option('password');
        $dryRun = $this->option('dry-run');
        
        $this->info('Scanning for broken password hashes...');
        
        // Find users with broken password hashes (from TURBO import)
        $brokenUsers = User::where('password', 'like', '$2y$04$' . str_repeat('a', 53))->get();
        
        if ($brokenUsers->isEmpty()) {
            $this->info('✅ No broken password hashes found. All passwords are valid.');
            return 0;
        }
        
        $this->warn("Found {$brokenUsers->count()} users with broken password hashes:");
        
        // Show affected users
        $this->table(
            ['ID', 'Name', 'Email', 'School'],
            $brokenUsers->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->school ? $user->school->name : 'No school'
                ];
            })->toArray()
        );
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN: Would fix all passwords with: ' . $defaultPassword);
            return 0;
        }
        
        if (!$this->confirm("Do you want to fix all {$brokenUsers->count()} broken passwords with: {$defaultPassword}?")) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        $this->info('Fixing broken password hashes...');
        $progressBar = $this->output->createProgressBar($brokenUsers->count());
        
        $fixed = 0;
        foreach ($brokenUsers as $user) {
            $user->password = Hash::make($defaultPassword);
            $user->save();
            $fixed++;
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        
        $this->info("✅ Successfully fixed {$fixed} broken password hashes.");
        $this->info("All users can now login with email and password: {$defaultPassword}");
        
        return 0;
    }
}

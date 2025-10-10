# Database Migration Instructions

## Overview

This document provides step-by-step instructions for updating your database schema to support the new school management features.

## Option 1: Fresh Migration (Development/Testing Only)

⚠️ **WARNING**: This will delete all existing data!

```bash
# Step 1: Rollback all migrations
php artisan migrate:rollback

# Step 2: Run migrations
php artisan migrate

# Step 3: Seed the database (optional)
php artisan db:seed --class=SchoolSeeder
```

## Option 2: Update Existing Database (Production Safe)

Use this option if you have existing data that needs to be preserved.

### Step 1: Backup Your Database

```bash
# For MySQL/MariaDB
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Or using Laravel
php artisan backup:run
```

### Step 2: Create a New Migration

```bash
php artisan make:migration update_schools_table_add_new_fields
```

### Step 3: Edit the Migration File

Open the newly created migration file in `database/migrations/` and add:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Update existing 'Non Formal' records to 'PKBM'
        DB::table('schools')
            ->where('education_level', 'Non Formal')
            ->update(['education_level' => 'PKBM']);

        // Step 2: Modify education_level enum to include new values
        // For MySQL 8.0+
        DB::statement("ALTER TABLE schools MODIFY COLUMN education_level ENUM('TK', 'SD', 'SMP', 'KB', 'PKBM') NOT NULL");

        // For older MySQL versions, you might need to do this:
        // DB::statement("ALTER TABLE schools CHANGE education_level education_level ENUM('TK', 'SD', 'SMP', 'KB', 'PKBM') NOT NULL");

        // Step 3: Add new fields
        Schema::table('schools', function (Blueprint $table) {
            $table->string('desa', 100)->nullable()->after('address');
            $table->string('kecamatan', 100)->nullable()->after('desa');
            $table->string('kabupaten_kota', 100)->nullable()->after('kecamatan');
            $table->string('provinsi', 100)->nullable()->after('kabupaten_kota');
            $table->decimal('latitude', 10, 8)->nullable()->after('provinsi');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        // Optional: Migrate existing region data to kecamatan
        DB::statement("UPDATE schools SET kecamatan = region WHERE region IS NOT NULL AND kecamatan IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new columns
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'desa',
                'kecamatan',
                'kabupaten_kota',
                'provinsi',
                'latitude',
                'longitude'
            ]);
        });

        // Revert education levels
        DB::table('schools')
            ->where('education_level', 'PKBM')
            ->update(['education_level' => 'Non Formal']);

        DB::table('schools')
            ->where('education_level', 'KB')
            ->update(['education_level' => 'TK']); // Map KB back to TK as fallback

        // Revert enum
        DB::statement("ALTER TABLE schools MODIFY COLUMN education_level ENUM('TK', 'SD', 'SMP', 'Non Formal') NOT NULL");
    }
};
```

### Step 4: Run the Migration

```bash
php artisan migrate
```

### Step 5: Verify the Changes

```bash
# Check the schools table structure
php artisan tinker
>>> Schema::getColumnListing('schools')
>>> \DB::select('SHOW COLUMNS FROM schools WHERE Field = "education_level"')
>>> exit

# Or using MySQL directly
mysql -u username -p database_name
DESCRIBE schools;
SELECT DISTINCT education_level FROM schools;
```

## Post-Migration Tasks

### 1. Update Existing School Records

You may want to populate the new fields for existing schools:

```php
// Example script to update schools
php artisan tinker

// Set default province for all schools
\App\Models\School::whereNull('provinsi')->update(['provinsi' => 'Sumatera Utara']);

// Set default city for all schools
\App\Models\School::whereNull('kabupaten_kota')->update(['kabupaten_kota' => 'Pematang Siantar']);

// Copy region to kecamatan if not set
\App\Models\School::whereNull('kecamatan')->update(['kecamatan' => \DB::raw('region')]);
```

### 2. Update Education Levels

If you have KB or PKBM schools that were marked as "Non Formal", update them:

```sql
-- Run this SQL directly or via Tinker
UPDATE schools
SET education_level = 'KB'
WHERE name LIKE '%KB%' AND education_level = 'PKBM';

UPDATE schools
SET education_level = 'PKBM'
WHERE name LIKE '%PKBM%' AND education_level = 'TK';
```

### 3. Test the Application

-   [ ] Test creating a new school
-   [ ] Test editing an existing school
-   [ ] Test Excel import with new template
-   [ ] Verify statistics display correctly
-   [ ] Check map displays on detail pages (when coordinates are set)

## Troubleshooting

### Error: "Data truncated for column 'education_level'"

This means you have existing data that doesn't match the new enum values. First update the data:

```sql
UPDATE schools SET education_level = 'PKBM' WHERE education_level = 'Non Formal';
```

Then run the migration again.

### Error: "Duplicate column name"

This means the migration was partially run. Either:

1. Rollback and try again: `php artisan migrate:rollback --step=1`
2. Or manually drop the columns that were created and run again

### Enum Modification Not Working

For older MySQL versions, try this alternative:

```sql
-- Create temporary column
ALTER TABLE schools ADD COLUMN education_level_new ENUM('TK', 'SD', 'SMP', 'KB', 'PKBM') NOT NULL DEFAULT 'SD';

-- Copy data
UPDATE schools SET education_level_new = education_level;

-- Drop old column
ALTER TABLE schools DROP COLUMN education_level;

-- Rename new column
ALTER TABLE schools CHANGE education_level_new education_level ENUM('TK', 'SD', 'SMP', 'KB', 'PKBM') NOT NULL;
```

## Verification Checklist

After migration, verify:

-   [ ] All schools have valid education_level values
-   [ ] New fields (desa, kecamatan, kabupaten_kota, provinsi) exist in database
-   [ ] Latitude and longitude fields accept decimal values
-   [ ] Forms show all new fields
-   [ ] Excel import template includes new columns
-   [ ] Statistics page shows 5 education levels (not 4)
-   [ ] Map displays when coordinates are present

## Rollback Procedure

If something goes wrong:

```bash
# Rollback the last migration
php artisan migrate:rollback --step=1

# Restore from backup
mysql -u username -p database_name < backup_YYYYMMDD_HHMMSS.sql
```

## Support

If you encounter issues:

1. Check `storage/logs/laravel.log` for detailed error messages
2. Verify MySQL version: `SELECT VERSION();`
3. Ensure database user has ALTER privileges
4. Test on development environment first

## Summary of Changes

| Field          | Type          | Nullable | Description                    |
| -------------- | ------------- | -------- | ------------------------------ |
| desa           | varchar(100)  | Yes      | Village name                   |
| kecamatan      | varchar(100)  | Yes      | District name (now user input) |
| kabupaten_kota | varchar(100)  | Yes      | Regency/City name              |
| provinsi       | varchar(100)  | Yes      | Province name                  |
| latitude       | decimal(10,8) | Yes      | GPS latitude                   |
| longitude      | decimal(11,8) | Yes      | GPS longitude                  |

| Education Level Changes                 |
| --------------------------------------- |
| Removed: Non Formal                     |
| Added: KB, PKBM                         |
| Total: 5 levels (TK, SD, SMP, KB, PKBM) |

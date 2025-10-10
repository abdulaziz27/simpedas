# School Management System Updates - Summary

## Changes Implemented

### 1. Education Levels Updated (TK, SD, SMP, KB, PKBM)

-   **Removed**: Non Formal
-   **Added**: KB, PKBM
-   **Total**: 5 education levels

### 2. New Fields Added to Schools Table

After the `address` field, the following fields were added:

-   `desa` (varchar 100, nullable) - Village
-   `kecamatan` (varchar 100, nullable) - District (now dynamic, user-input)
-   `kabupaten_kota` (varchar 100, nullable) - Regency/City
-   `provinsi` (varchar 100, nullable) - Province
-   `latitude` (decimal 10,8, nullable) - For map location
-   `longitude` (decimal 11,8, nullable) - For map location

### 3. Files Modified

#### Database & Models

-   ✅ `database/migrations/2025_07_03_182944_create_schools_table.php` - Migration updated
-   ✅ `app/Models/School.php` - Fillable fields updated
-   ✅ `database/factories/SchoolFactory.php` - Factory updated
-   ✅ `database/seeders/SchoolSeeder.php` - Seeder updated

#### Configuration

-   ✅ `config/school.php` - Education levels updated

#### Controllers

-   ✅ `app/Http/Controllers/Admin/SchoolController.php` - Validation rules updated for store/update
-   ✅ `app/Http/Controllers/PublicController.php` - Statistics chart data updated
-   ✅ `app/Http/Controllers/Admin/ReportController.php` - School reports updated

#### Views

-   ✅ `resources/views/admin/schools/create.blade.php` - Form fields added
-   ✅ `resources/views/admin/schools/edit.blade.php` - Form fields added
-   ✅ `resources/views/admin/schools/show.blade.php` - Display fields + Leaflet.js map added
-   ✅ `resources/views/admin/schools/print.blade.php` - Print view updated with new fields
-   ✅ `resources/views/public/detail-sekolah.blade.php` - Public detail view updated with new fields + map

#### Import/Export (Excel)

-   ✅ `app/Imports/SchoolImport.php` - Import logic updated
-   ✅ `app/Exports/SchoolTemplateExport.php` - Excel template updated

### 4. Leaflet.js Map Implementation

-   Added interactive map at the bottom of school detail page
-   Shows school location if latitude and longitude are provided
-   Uses OpenStreetMap tiles
-   Hidden during print

## Database Migration Instructions

### Important: Rollback and Re-migrate

Since we modified the existing migration file, you need to rollback and re-run migrations:

```bash
# 1. Rollback all migrations
php artisan migrate:rollback

# 2. Re-run migrations
php artisan migrate

# 3. (Optional) Seed the database with sample data
php artisan db:seed --class=SchoolSeeder
```

### Alternative: If you have existing data and cannot rollback

If you already have important data in production, you'll need to create a new migration to add the fields:

```bash
php artisan make:migration add_new_fields_to_schools_table
```

Then add this content to the migration:

```php
public function up()
{
    Schema::table('schools', function (Blueprint $table) {
        // Modify education_level enum
        DB::statement("ALTER TABLE schools MODIFY COLUMN education_level ENUM('TK', 'SD', 'SMP', 'KB', 'PKBM') NOT NULL");

        // Add new fields after address
        $table->string('desa', 100)->nullable()->after('address');
        $table->string('kecamatan', 100)->nullable()->after('desa');
        $table->string('kabupaten_kota', 100)->nullable()->after('kecamatan');
        $table->string('provinsi', 100)->nullable()->after('kabupaten_kota');
        $table->decimal('latitude', 10, 8)->nullable()->after('provinsi');
        $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
    });
}

public function down()
{
    Schema::table('schools', function (Blueprint $table) {
        $table->dropColumn(['desa', 'kecamatan', 'kabupaten_kota', 'provinsi', 'latitude', 'longitude']);
        DB::statement("ALTER TABLE schools MODIFY COLUMN education_level ENUM('TK', 'SD', 'SMP', 'Non Formal') NOT NULL");
    });
}
```

## Testing Checklist

### Manual Form Testing

-   [ ] Create new school with all fields
-   [ ] Edit existing school and add new location fields
-   [ ] Verify map displays on detail page when latitude/longitude are set
-   [ ] Test with coordinates: Pematang Siantar area (Lat: ~2.96, Long: ~99.06)

### Excel Import/Export Testing

-   [ ] Download new Excel template
-   [ ] Verify dropdown shows: TK, SD, SMP, KB, PKBM
-   [ ] Import schools with new fields
-   [ ] Verify validation works for new education levels

### Statistics Testing

-   [ ] Check dashboard statistics reflect 5 education levels
-   [ ] Verify public statistics page shows correct data
-   [ ] Check reports show KB and PKBM instead of Non Formal

## Notes

1. **Region Field**: The old `region` field (kecamatan dropdown) is kept for backward compatibility but marked as "Legacy" in forms. The new `kecamatan` field is now a free-text input for flexibility.

2. **Map Library**: Using Leaflet.js (open-source, lightweight, no API key required)

3. **Coordinates**: For Pematang Siantar area, typical coordinates are:

    - Latitude: 2.9XXX (range: 2.9 to 3.0)
    - Longitude: 99.0XXX (range: 99.0 to 99.1)

4. **Validation**: Latitude must be between -90 and 90, Longitude between -180 and 180

## Recommended Next Steps

1. Backup your current database before migration
2. Run the migration commands
3. Test the new forms and import functionality
4. Update any existing schools with the new location data
5. Monitor for any issues with existing reports or statistics

## Support

If you encounter any issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check database constraints and enum values
3. Verify Leaflet.js loads correctly (check browser console)
4. Ensure old data with "Non Formal" is updated to either "KB" or "PKBM"

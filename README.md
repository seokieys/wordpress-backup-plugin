# Backup to S3 Plugin Documentation (v1.0)

## Plugin Name
**Backup to S3**  
A WordPress plugin to backup WordPress files and database to AWS S3.

## Version
**1.0**

## Author
Nate Seong

## Description
The **Backup to S3** plugin allows you to back up your WordPress site's database and files and store them securely on AWS S3. This plugin supports WordPress Multisite configurations and provides functionality to:

1. Back up the database using `mysqldump`.
2. Compress WordPress files (excluding the backup directory) into a ZIP archive.
3. Upload the database and file backups to an AWS S3 bucket.
4. Automatically clean up older backup files to save storage space.
5. Schedule automatic daily backups with WordPress Cron.
6. Perform manual backups or test S3 uploads from the plugin settings page.

## Features
- **WordPress Multisite Support**: Designed to function seamlessly in a WordPress Multisite environment. It backs up all network sites in a single action.
- **AWS S3 Integration**: Backups are uploaded directly to your AWS S3 bucket.
- **Backup Management**: Older backups are deleted from the local host after successful S3 uploads, ensuring optimal storage usage.
- **Manual Backup**: You can run backups manually from the plugin's settings page.
- **Test S3 Upload**: A test button is available to validate your S3 connection and configuration.
- **Automated Daily Backups**: Uses WordPress Cron to schedule backups.

## Installation
1. Download the plugin files and ensure they are in a ZIP format.
2. Log in to your WordPress admin dashboard as a network administrator.
3. Navigate to `Plugins > Add New` and upload the ZIP file.
4. Activate the plugin from the **Network Admin Plugins** page.

## Configuration
1. Navigate to the plugin settings page under `Backup to S3` in the Network Admin dashboard.
2. Enter the required AWS credentials and bucket details:
   - **AWS Key**: Your AWS access key.
   - **AWS Secret**: Your AWS secret key.
   - **Bucket Name**: The S3 bucket name where backups will be stored.
   - **Region**: The AWS region of your bucket.
3. Save your settings.

## How It Works
1. **Database Backup**:
   - Utilizes the `mysqldump` command to export the WordPress database.
   - Works with AWS RDS databases (ensure proper permissions and access).
   
2. **File Backup**:
   - Compresses the WordPress `wp-content` directory into a ZIP file.
   - Excludes the `/backups` directory to prevent recursion.

3. **S3 Upload**:
   - The backups are uploaded to the specified AWS S3 bucket with filenames containing timestamps for unique identification.

4. **Cleanup**:
   - Older backup files are automatically deleted from the host system to save storage.

## Plugin Settings Page
The plugin settings page includes:
- Fields for AWS credentials and S3 bucket configuration.
- A **Run Backup Now** button for manual backups.
- A **Test S3 Upload** button to verify your S3 configuration.

## Notes
- This plugin requires PHP 8.0 or higher.
- `mysqldump` must be available on your server for database backups to work.
- Ensure your AWS credentials have the necessary permissions to upload to S3.
- If using AWS RDS for your database, verify that the WordPress container has network access to the RDS instance.

## Changelog
**v1.0**:
- Initial release.
- Added support for WordPress Multisite.
- Integrated AWS S3 for file and database backups.
- Implemented manual and scheduled backup functionalities.
- Added automatic cleanup for older backups.

## License
This plugin is released under the GPL v2 or later license.

## Support
For questions, suggestions, or bug reports, please contact Nate Seong.
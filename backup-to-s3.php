<?php
/*
Plugin Name: Backup to S3
Description: A plugin to backup WordPress files and database to AWS S3.
Version: 1.0
Author: Nate Seong
*/

require_once __DIR__ . '/vendor/autoload.php';
use Aws\S3\S3Client;

// Helper function to ensure backup directory exists
function ensure_backup_directory_exists() {
    $backup_dir = WP_CONTENT_DIR . '/backups';
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }
}

// Create database backup
function create_db_backup($output_file) {
    ensure_backup_directory_exists();
    global $wpdb;
    $db_host = DB_HOST;
    $db_name = DB_NAME;
    $db_user = DB_USER;
    $db_password = DB_PASSWORD;

    $command = "mysqldump --host=$db_host --user=$db_user --password=$db_password $db_name > $output_file";
    exec($command);

    if (!file_exists($output_file)) {
        error_log("Database backup failed: $output_file was not created.");
    }
}

// Create files backup
function create_files_backup($output_file) {
    ensure_backup_directory_exists();
    $zip = new ZipArchive();
    if ($zip->open($output_file, ZipArchive::CREATE) === TRUE) {
        $rootPath = realpath(WP_CONTENT_DIR);
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAF_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
    }
}

// Upload backup to S3
function upload_to_s3($file_path, $bucket_name, $s3_key) {
    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => AWS_REGION,
        'credentials' => [
            'key'    => AWS_ACCESS_KEY,
            'secret' => AWS_SECRET_KEY,
        ],
    ]);

    $result = $s3->putObject([
        'Bucket' => $bucket_name,
        'Key'    => $s3_key,
        'SourceFile' => $file_path,
    ]);

    return $result['ObjectURL'];
}

// Perform backup
add_action('backup_to_s3_cron_job', 'perform_backup');
function perform_backup() {
    $db_backup = WP_CONTENT_DIR . '/backups/db_backup.sql';
    $files_backup = WP_CONTENT_DIR . '/backups/files_backup.zip';

    create_db_backup($db_backup);
    create_files_backup($files_backup);

    upload_to_s3($db_backup, AWS_BUCKET_NAME, 'backups/db_backup.sql');
    upload_to_s3($files_backup, AWS_BUCKET_NAME, 'backups/files_backup.zip');
}

// Schedule Cron
if (!wp_next_scheduled('backup_to_s3_cron_job')) {
    wp_schedule_event(time(), 'daily', 'backup_to_s3_cron_job');
}

// Add admin menu for manual backup and settings
add_action('admin_menu', function() {
    add_options_page(
        'Backup to S3 Settings',
        'Backup to S3',
        'manage_options',
        'backup-to-s3',
        'backup_to_s3_settings_page'
    );
});

function backup_to_s3_settings_page() {
  if ($_POST['aws_settings']) {
      update_option('backup_to_s3_aws_key', sanitize_text_field($_POST['aws_key']));
      update_option('backup_to_s3_aws_secret', sanitize_text_field($_POST['aws_secret']));
      update_option('backup_to_s3_bucket', sanitize_text_field($_POST['bucket']));
  }
  ?>
  <form method="POST">
      <label>AWS Key: <input type="text" name="aws_key" value="<?php echo get_option('backup_to_s3_aws_key'); ?>"></label><br>
      <label>AWS Secret: <input type="text" name="aws_secret" value="<?php echo get_option('backup_to_s3_aws_secret'); ?>"></label><br>
      <label>Bucket Name: <input type="text" name="bucket" value="<?php echo get_option('backup_to_s3_bucket'); ?>"></label><br>
      <input type="submit" name="aws_settings" value="Save Settings" class="button-primary">
  </form>
  <?php
}
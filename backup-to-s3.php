<?php
/*
Plugin Name: Backup to S3
Description: A plugin to backup WordPress files and database to AWS S3.
Version: 1.0
Author: Nate Seong
*/

require_once __DIR__ . '/vendor/autoload.php';
use Aws\S3\S3Client;

register_activation_hook(__FILE__, 'create_backup_htaccess');

// 플러그인 활성화 시 Cron 작업 추가
register_activation_hook(__FILE__, 'add_backup_to_s3_cron');

// 플러그인 비활성화 시 Cron 작업 삭제
register_deactivation_hook(__FILE__, 'delete_backup_to_s3_cron');

function add_backup_to_s3_cron() {
    if (!wp_next_scheduled('backup_to_s3_cron_job')) {
        wp_schedule_event(time(), 'daily', 'backup_to_s3_cron_job');
    }
}

function delete_backup_to_s3_cron() {
    wp_clear_scheduled_hook('backup_to_s3_cron_job');
}

function create_backup_htaccess() {
    $backup_dir = WP_CONTENT_DIR . '/backups';

    // 백업 디렉토리 확인 및 생성
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }

    // .htaccess 파일 경로 설정
    $htaccess_file = $backup_dir . '/.htaccess';

    // .htaccess 내용
    $htaccess_content = <<<EOD
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule .* - [R=404,L]
</IfModule>
EOD;

    // .htaccess 파일 생성 또는 업데이트
    file_put_contents($htaccess_file, $htaccess_content);
}

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

    // RDS 정보 가져오기
    $db_host = getenv('WORDPRESS_DB_HOST');      // RDS 엔드포인트
    $db_name = getenv('WORDPRESS_DB_NAME');      // 데이터베이스 이름
    $db_user = getenv('WORDPRESS_DB_USER');      // 사용자 이름
    $db_password = getenv('WORDPRESS_DB_PASSWORD'); // 비밀번호

    // mysqldump 명령어 실행
    $command = "mysqldump --host=$db_host --user=$db_user --password=$db_password $db_name > $output_file 2>&1";
    $output = [];
    $return_var = null;

    exec($command, $output, $return_var);

    if ($return_var !== 0) {
        error_log("mysqldump failed: " . implode("\n", $output));
    }

    if (!file_exists($output_file) || filesize($output_file) === 0) {
        error_log("Database backup failed: $output_file was not created or is empty.");
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
            0 // RecursiveIteratorIterator::LEAF_ONLY 대신 숫자 0 사용
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // backups 디렉토리 제외
                if (strpos($relativePath, 'backups/') === 0) {
                    continue; // 이 파일을 건너뜁니다
                }

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
        'region'  => get_option('backup_to_s3_region'), // 저장된 리전 값 사용
        'credentials' => [
            'key'    => get_option('backup_to_s3_aws_key'),
            'secret' => get_option('backup_to_s3_aws_secret'),
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
function perform_backup() {
    $bucket_name = get_option('backup_to_s3_bucket');
    $region = get_option('backup_to_s3_region');

    if (!$bucket_name) {
        error_log('Error: S3 Bucket Name is not set.');
        return;
    }

    if (!$region) {
        error_log('Error: S3 Region is not set.');
        return;
    }

    // 파일명에 타임스탬프 추가 (밀리초 단위)
    $timestamp = round(microtime(true) * 1000);
    $db_backup = WP_CONTENT_DIR . "/backups/db_backup_{$timestamp}.sql";
    $files_backup = WP_CONTENT_DIR . "/backups/files_backup_{$timestamp}.zip";

    create_db_backup($db_backup);
    create_files_backup($files_backup);

    // S3 업로드
    upload_to_s3($db_backup, $bucket_name, "backups/db_backup_{$timestamp}.sql");
    upload_to_s3($files_backup, $bucket_name, "backups/files_backup_{$timestamp}.zip");

    // 이전 백업 파일 삭제 (현재 파일 제외)
    delete_old_backups(WP_CONTENT_DIR . '/backups', [$db_backup, $files_backup]);
}

function perform_backup_for_all_sites() {
    global $wpdb;

    // Get all sites in the network
    $sites = get_sites();
    foreach ($sites as $site) {
        switch_to_blog($site->blog_id); // Switch to the site
        perform_backup(); // Run the backup function
        restore_current_blog(); // Restore to the current site
    }
}

function delete_old_backups($backup_dir, $exclude_files = []) {
    if (!is_dir($backup_dir)) {
        return; // 백업 디렉토리가 없으면 아무 작업도 하지 않음
    }

    $files = scandir($backup_dir);
    foreach ($files as $file) {
        $file_path = $backup_dir . '/' . $file;

        // 현재 파일인지 확인
        if (in_array($file_path, $exclude_files)) {
            continue; // 제외할 파일은 건너뜀
        }

        // 파일인지 확인하고, 이름에 'db_backup_' 또는 'files_backup_'가 포함된 경우만 처리
        if (is_file($file_path) && (strpos($file, 'db_backup_') === 0 || strpos($file, 'files_backup_') === 0)) {
            unlink($file_path); // 파일 삭제
        }
    }
}

function backup_to_s3_settings_page() {
    // AWS 설정 저장
    if (isset($_POST['aws_settings'])) {
        update_option('backup_to_s3_aws_key', sanitize_text_field($_POST['aws_key']));
        update_option('backup_to_s3_aws_secret', sanitize_text_field($_POST['aws_secret']));
        update_option('backup_to_s3_bucket', sanitize_text_field($_POST['bucket']));
        update_option('backup_to_s3_region', sanitize_text_field($_POST['region']));
        echo '<div class="updated"><p>Settings saved successfully!</p></div>';
    }

    // 수동 백업 실행
    if (isset($_POST['run_backup'])) {
        perform_backup_for_all_sites();
        echo '<div class="updated"><p>Backup completed successfully!</p></div>';
    }

    // S3 테스트 파일 업로드
    if (isset($_POST['test_s3'])) {
        $test_file = WP_CONTENT_DIR . '/backups/test.txt';
        file_put_contents($test_file, 'S3 Test File Upload');
        upload_to_s3($test_file, get_option('backup_to_s3_bucket'), 'backups/test.txt');
        echo '<div class="updated"><p>Test file uploaded to S3 successfully!</p></div>';
    }

    // 설정 페이지 HTML 렌더링
    ?>
    <form method="POST">
        <h2>Backup to S3 Settings</h2>
        <label>AWS Key: <input type="text" name="aws_key" value="<?php echo esc_attr(get_option('backup_to_s3_aws_key')); ?>"></label><br>
        <label>AWS Secret: <input type="text" name="aws_secret" value="<?php echo esc_attr(get_option('backup_to_s3_aws_secret')); ?>"></label><br>
        <label>Bucket Name: <input type="text" name="bucket" value="<?php echo esc_attr(get_option('backup_to_s3_bucket')); ?>"></label><br>
        <label>Region: <input type="text" name="region" value="<?php echo esc_attr(get_option('backup_to_s3_region')); ?>"></label><br>
        <input type="submit" name="aws_settings" value="Save Settings" class="button-primary">
    </form>
    <hr>
    <form method="POST">
        <h2>Run Backup</h2>
        <input type="submit" name="run_backup" value="Run Backup Now" class="button-primary">
    </form>
    <hr>
    <form method="POST">
        <h2>Test S3 Upload</h2>
        <input type="submit" name="test_s3" value="Test S3 Upload" class="button-primary">
    </form>
    <?php
}

add_action('backup_to_s3_cron_job', 'perform_backup_for_all_sites');
// Add admin menu for manual backup and settings
add_action('network_admin_menu', function() {
    add_menu_page(
        'Backup to S3 Settings',      
        'Backup to S3',               
        'manage_network_options',     
        'backup-to-s3',               
        'backup_to_s3_settings_page',
        'dashicons-backup',
        80
    );
});

// Schedule Cron
if (!wp_next_scheduled('backup_to_s3_cron_job')) {
    wp_schedule_event(time(), 'daily', 'backup_to_s3_cron_job');
}
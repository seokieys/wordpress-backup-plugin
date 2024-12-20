<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Cron 작업 제거
wp_clear_scheduled_hook('backup_to_s3_cron_job');

// 옵션 데이터 삭제 (선택 사항)
delete_option('backup_to_s3_aws_key');
delete_option('backup_to_s3_aws_secret');
delete_option('backup_to_s3_bucket');
delete_option('backup_to_s3_region');
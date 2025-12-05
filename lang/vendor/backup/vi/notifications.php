<?php

return [
    'exception_message' => 'Thông báo ngoại lệ (Exception message): :message',
    'exception_trace' => 'Dấu vết ngoại lệ (Exception trace): :trace',
    'exception_message_title' => 'Thông báo ngoại lệ',
    'exception_trace_title' => 'Dấu vết ngoại lệ',

    'backup_failed_subject' => 'Sao lưu :application_name thất bại',
    'backup_failed_body' => 'Quan trọng: Đã xảy ra lỗi trong quá trình sao lưu :application_name',

    'backup_successful_subject' => 'Sao lưu mới :application_name thành công',
    'backup_successful_subject_title' => 'Sao lưu mới thành công!',
    'backup_successful_body' => 'Tin tốt: Một bản sao lưu mới của :application_name đã được tạo thành công trên ổ đĩa có tên :disk_name.',

    'cleanup_failed_subject' => 'Dọn dẹp các bản sao lưu của :application_name thất bại.',
    'cleanup_failed_body' => 'Đã xảy ra lỗi trong quá trình dọn dẹp các bản sao lưu của :application_name',

    'cleanup_successful_subject' => 'Dọn dẹp các bản sao lưu của :application_name thành công',
    'cleanup_successful_subject_title' => 'Dọn dẹp các bản sao lưu thành công!',
    'cleanup_successful_body' => 'Việc dọn dẹp các bản sao lưu của :application_name trên ổ đĩa có tên :disk_name đã thành công.',

    'healthy_backup_found_subject' => 'Các bản sao lưu cho :application_name trên ổ đĩa :disk_name đang ở trạng thái tốt (healthy)',
    'healthy_backup_found_subject_title' => 'Các bản sao lưu cho :application_name đang ở trạng thái tốt',
    'healthy_backup_found_body' => 'Các bản sao lưu cho :application_name được coi là ở trạng thái tốt. Làm tốt lắm!',

    'unhealthy_backup_found_subject' => 'Quan trọng: Các bản sao lưu cho :application_name đang ở trạng thái không tốt (unhealthy)',
    'unhealthy_backup_found_subject_title' => 'Quan trọng: Các bản sao lưu cho :application_name đang ở trạng thái không tốt. :problem',
    'unhealthy_backup_found_body' => 'Các bản sao lưu cho :application_name trên ổ đĩa :disk_name đang ở trạng thái không tốt.',
    'unhealthy_backup_found_not_reachable' => 'Không thể kết nối (truy cập) được đích sao lưu. :error',
    'unhealthy_backup_found_empty' => 'Hoàn toàn không có bản sao lưu nào của ứng dụng này.',
    'unhealthy_backup_found_old' => 'Bản sao lưu mới nhất được tạo vào :date được coi là quá cũ.',
    'unhealthy_backup_found_unknown' => 'Xin lỗi, không thể xác định được nguyên nhân chính xác.',
    'unhealthy_backup_found_full' => 'Các bản sao lưu đang sử dụng quá nhiều dung lượng lưu trữ. Mức sử dụng hiện tại là :disk_usage, cao hơn giới hạn cho phép là :disk_limit.',

    'no_backups_info' => 'Chưa có bản sao lưu nào được tạo',
    'application_name' => 'Tên ứng dụng',
    'backup_name' => 'Tên bản sao lưu',
    'disk' => 'Ổ đĩa',
    'newest_backup_size' => 'Kích thước bản sao lưu mới nhất',
    'number_of_backups' => 'Số lượng bản sao lưu',
    'total_storage_used' => 'Tổng dung lượng lưu trữ đã sử dụng',
    'newest_backup_date' => 'Ngày sao lưu mới nhất',
    'oldest_backup_date' => 'Ngày sao lưu cũ nhất',
];
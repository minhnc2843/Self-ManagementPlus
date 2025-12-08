# 🚀 Self-ManagementPlus

Self-ManagementPlus là một ứng dụng web được xây dựng bằng Laravel, tập trung vào các tính năng quản trị bản thân (Self-Management) và phát triển cá nhân.

---

## 🛠️ Yêu cầu hệ thống (Prerequisites)

* **PHP:** >= 8.1
* **Composer**
* **Node.js & npm**
* **Database:** MySQL
---

## ⚙️ Cài đặt và Khởi chạy Ứng dụng (Setup and Run)

Thực hiện theo các bước sau để cài đặt và khởi chạy ứng dụng trên môi trường cục bộ (local environment):

### 1. Clone Project và Cấu hình

Sử dụng Git để tải mã nguồn về máy:

```bash
# Clone repository
git clone https://github.com/minhnc2843/Self-ManagementPlus.git

# Di chuyển vào thư mục dự án
cd Self-ManagementPlus

# Tạo file cấu hình môi trường (.env)
cp .env.example .env
# Cài đặt các thư viện PHP
composer install

# Cài đặt các thư viện Node.js (Front-end assets)
npm install

# Tạo khóa ứng dụng (App Key)
php artisan key:generate

# Chạy migration để tạo bảng
php artisan migrate

# Chạy seeder để điền dữ liệu mẫu (tuỳ chọn)
php artisan db:seed

# Tạo liên kết symbolic link cho storage (ảnh, file upload)
php artisan storage:link

#run server
npm run dev 
php artisan ser


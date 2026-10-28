<?php
// web/admin/login.php
// Chuyển hướng tới trang Đăng nhập hợp nhất dành cho cả Khách hàng & Quản trị viên
header("Location: ../login.php?role=admin");
exit;

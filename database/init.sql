-- Database Initialization for Dental Clinic System

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS `dental_clinic` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `dental_clinic`;

-- 1. Table: admins
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `chatbot_messages`;
DROP TABLE IF EXISTS `patients`;
DROP TABLE IF EXISTS `doctors`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `admins`;

CREATE TABLE `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table: patients
CREATE TABLE `patients` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL,
    `date_of_birth` DATE DEFAULT NULL,
    `address` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_patient_phone` (`phone`),
    INDEX `idx_patient_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table: doctors
CREATE TABLE `doctors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `specialty` VARCHAR(100) NOT NULL,
    `experience` VARCHAR(50) NOT NULL,
    `description` TEXT,
    `image` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table: services
CREATE TABLE `services` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(12, 2) NOT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Table: appointments
CREATE TABLE `appointments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `doctor_id` INT DEFAULT NULL,
    `service_id` INT DEFAULT NULL,
    `appointment_date` DATE NOT NULL,
    `appointment_time` TIME NOT NULL,
    `note` TEXT,
    `status` ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE SET NULL,
    INDEX `idx_appointment_date` (`appointment_date`),
    INDEX `idx_appointment_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Table: chatbot_messages
CREATE TABLE `chatbot_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `session_id` VARCHAR(100) DEFAULT NULL,
    `user_message` TEXT NOT NULL,
    `ai_response` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- SAMPLE DATA INSERTION
-- ==========================================

-- Admin account (Password is 'admin123' hashed with bcrypt)
INSERT INTO `admins` (`username`, `password`, `full_name`) VALUES
('admin', '$2y$10$qW.8pB9F044uW/75s2QWVeJj/3vA.s6tU6jL2lT3z1F/zS7uS8.m2', 'Quản trị viên Hệ thống');
-- Note: 'admin123' bcrypt hash generated above.

-- Sample Doctors (5 Doctors)
INSERT INTO `doctors` (`full_name`, `specialty`, `experience`, `description`, `image`) VALUES
('ThS. BS. Nguyễn Văn An', 'Răng Hàm Mặt Tổng Quát', '12 năm kinh nghiệm', 'Tốt nghiệp Đại học Y Dược TP.HCM, chứng chỉ Chuyên khoa Răng Hàm Mặt quốc tế. Chuyên gia tư vấn tổng thể và lập kế hoạch điều trị toàn diện.', 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=400&auto=format&fit=crop&q=80'),
('BS. CKI. Trần Thị Mai', 'Chỉnh Hình Răng (Niềng Răng)', '8 năm kinh nghiệm', 'Chuyên gia nắn chỉnh răng không mắc cài Invisalign & niềng răng mắc cài kim loại/sứ thẩm mỹ. Đã điều trị thành công hơn 1.000 ca nha khoa.', 'https://images.unsplash.com/photo-1594824813566-88855ce78347?w=400&auto=format&fit=crop&q=80'),
('ThS. BS. Lê Hoàng Nam', 'Cấy Ghép Implant Nha Khoa', '15 năm kinh nghiệm', 'Tu nghiệp cấy ghép nha khoa tại Thụy Sĩ. Thực hiện hàng ngàn ca cấy ghép Implant đơn lẻ và toàn hàm không đau, lành thương nhanh.', 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?w=400&auto=format&fit=crop&q=80'),
('BS. Phạm Thùy Linh', 'Nha Khoa Thẩm Mỹ & Phục Hình Sứ', '7 năm kinh nghiệm', 'Chuyên sâu thiết kế nụ cười Smile Design, dán sứ Veneer và bọc răng sứ thẩm mỹ bảo tồn răng thật tối đa.', 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&auto=format&fit=crop&q=80'),
('BS. CKI. Đỗ Minh Đức', 'Phẫu Thuật Tiểu Phẫu & Nhổ Răng Khôn', '10 năm kinh nghiệm', 'Kỹ thuật nhổ răng khôn bằng máy Piezotome không đau, an toàn tuyệt đối. Chuyên sâu xử lý các ca răng lệch, mọc ngầm phức tạp.', 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&auto=format&fit=crop&q=80');

-- Sample Services (8 Services)
INSERT INTO `services` (`name`, `description`, `price`, `image`) VALUES
('Khám & Tư vấn Răng tổng quát', 'Khám kiểm tra tổng thể sức khỏe răng miệng, chụp X-quang và tư vấn hướng điều trị miễn phí.', 100000.00, 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=400&auto=format&fit=crop&q=80'),
('Cạo vôi răng & Đánh bóng', 'Làm sạch mảng bám, vôi răng bằng sóng siêu âm êm ái, hạn chế ê buốt và mang lại hơi thở thơm mát.', 250000.00, 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=400&auto=format&fit=crop&q=80'),
('Trám răng thẩm mỹ Composite', 'Tái tạo hình dáng răng sâu, răng sứt mẻ bằng chất liệu Composite cao cấp đồng màu răng thật.', 350000.00, 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=400&auto=format&fit=crop&q=80'),
('Nhổ răng thường / Răng khôn', 'Nhổ răng an toàn bằng công nghệ sóng siêu âm Piezotome, không đau, giảm sưng nát mô mềm.', 500000.00, 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=400&auto=format&fit=crop&q=80'),
('Niềng răng Chỉnh nha', 'Khắc phục răng hô, móm, lệch lạc với mắc cài kim loại/sứ hoặc khay trong suốt Invisalign.', 25000000.00, 'https://images.unsplash.com/photo-1598256989800-fe5f95da9787?w=400&auto=format&fit=crop&q=80'),
('Tẩy trắng răng Laser Whitening', 'Công nghệ chiếu sáng Laser kích hoạt gel tẩy trắng, mang lại răng trắng sáng bật 3-5 tông.', 2000000.00, 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=400&auto=format&fit=crop&q=80'),
('Bọc răng sứ thẩm mỹ', 'Bọc răng sứ Zirconia/Cercon Đức cao cấp, tự nhiên như răng thật, độ bền lên tới 20 năm.', 3500000.00, 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=400&auto=format&fit=crop&q=80'),
('Cấy ghép Implant Nha Khoa', 'Phục hồi răng đã mất hoàn hảo với trụ Implant cao cấp Thụy Sĩ/Hàn Quốc tích hợp xương nhanh.', 14000000.00, 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=400&auto=format&fit=crop&q=80');

-- Sample Patients (5 Patients)
INSERT INTO `patients` (`full_name`, `phone`, `email`, `date_of_birth`, `address`) VALUES
('Nguyễn Văn Tuấn', '0912345678', 'tuan.nguyen@gmail.com', '1990-05-15', '123 Nguyễn Trãi, Quận 5, TP.HCM'),
('Lê Thị Hoa', '0987654321', 'hoa.le@gmail.com', '1995-08-20', '456 Lê Văn Sỹ, Quận 3, TP.HCM'),
('Trần Hoàng Nam', '0903112233', 'nam.tran@gmail.com', '1988-11-02', '789 Điện Biên Phủ, Bình Thạnh, TP.HCM'),
('Pham Minh Anh', '0938889900', 'minhanh.pham@gmail.com', '2001-03-10', '12 Võ Văn Tần, Quận 3, TP.HCM'),
('Đặng Quốc Khánh', '0977665544', 'khanh.dang@gmail.com', '1993-12-25', '88 Trần Hưng Đạo, Quận 1, TP.HCM');

-- Sample Appointments (5 Appointments with status pending, confirmed, completed, cancelled)
INSERT INTO `appointments` (`patient_id`, `doctor_id`, `service_id`, `appointment_date`, `appointment_time`, `note`, `status`) VALUES
(1, 1, 1, CURRENT_DATE(), '09:00:00', 'Khám định kỳ tổng quát', 'confirmed'),
(2, 2, 5, CURRENT_DATE(), '10:30:00', 'Tư vấn niềng răng mắc cài sứ', 'pending'),
(3, 3, 8, CURRENT_DATE() + INTERVAL 1 DAY, '14:00:00', 'Khám tư vấn cấy ghép Implant răng 36', 'confirmed'),
(4, 4, 6, CURRENT_DATE() - INTERVAL 1 DAY, '15:30:00', 'Tẩy trắng răng Laser Whitening', 'completed'),
(5, 5, 4, CURRENT_DATE() + INTERVAL 2 DAY, '11:00:00', 'Nhổ răng khôn hàm dưới bên trái', 'cancelled');

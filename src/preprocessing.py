"""
Module Tiền xử lý ảnh X-Quang Nha Khoa (Dental Panoramic Radiograph Preprocessing)
Sử dụng OpenCV và NumPy để tăng cường chất lượng ảnh X-quang.

Tác giả: Ngô Xuân Minh Hiếu
Bài tập lớn: Hệ thống quản lý nha khoa tích hợp AI - Tuần 1
"""

import cv2
import numpy as np
from typing import Tuple, Dict, Any


def load_xray_image(image_path: str, as_grayscale: bool = True) -> np.ndarray:
    """
    Đọc ảnh X-quang nha khoa từ tệp.
    
    :param image_path: Đường dẫn tới tệp ảnh
    :param as_grayscale: Chuyển đổi thành ảnh xám (Grayscale) nếu True
    :return: Mảng numpy chứa ảnh
    """
    flag = cv2.IMREAD_GRAYSCALE if as_grayscale else cv2.IMREAD_COLOR
    img = cv2.imread(image_path, flag)
    if img is None:
        raise FileNotFoundError(f"Không thể đọc ảnh tại đường dẫn: {image_path}")
    return img


def apply_clahe(image: np.ndarray, clip_limit: float = 2.5, tile_grid_size: Tuple[int, int] = (8, 8)) -> np.ndarray:
    """
    Áp dụng thuật toán CLAHE (Contrast Limited Adaptive Histogram Equalization)
    Đặc biệt hiệu quả với ảnh X-quang panorama giúp làm rõ ranh giới men răng,
    tổn thương sâu răng và vùng tiêu xương cuống răng.
    
    :param image: Ảnh đầu vào (Grayscale hoặc BGR)
    :param clip_limit: Ngưỡng cắt đỉnh histogram để chống khuếch đại nhiễu (mặc định 2.5)
    :param tile_grid_size: Kích thước lưới ma trận xử lý cục bộ (mặc định 8x8)
    :return: Ảnh sau khi cân bằng tương phản
    """
    if len(image.shape) == 3:
        # Nếu là ảnh màu, chuyển sang kênh LAB và áp dụng CLAHE lên kênh L (Lightness)
        lab = cv2.cvtColor(image, cv2.COLOR_BGR2LAB)
        l, a, b = cv2.split(lab)
        clahe = cv2.createCLAHE(clipLimit=clip_limit, tileGridSize=tile_grid_size)
        cl = clahe.apply(l)
        limg = cv2.merge((cl, a, b))
        enhanced = cv2.cvtColor(limg, cv2.COLOR_LAB2BGR)
        return enhanced
    else:
        clahe = cv2.createCLAHE(clipLimit=clip_limit, tileGridSize=tile_grid_size)
        return clahe.apply(image)


def denoise_xray(image: np.ndarray, h: float = 7.0, template_window: int = 7, search_window: int = 21) -> np.ndarray:
    """
    Khử nhiễu ảnh X-quang bằng thuật toán Non-Local Means Denoising.
    Giúp loại bỏ nhiễu hạt (speckle noise) do máy chụp X-quang mà không làm mờ biên giới răng.
    
    :param image: Ảnh xám đầu vào
    :param h: Tham số quyết định lọc nhiễu (h càng cao càng sạch nhiễu nhưng dễ mất nét)
    """
    if len(image.shape) == 3:
        return cv2.fastNlMeansDenoisingColored(image, None, h, h, template_window, search_window)
    return cv2.fastNlMeansDenoising(image, None, h, template_window, search_window)


def letterbox_resize(image: np.ndarray, target_shape: Tuple[int, int] = (640, 640), fill_value: int = 0) -> Tuple[np.ndarray, float, Tuple[int, int]]:
    """
    Resize ảnh X-quang Panorama (thường có tỉ lệ rộng 2:1) về kích thước vuông cho YOLOv8 / CNN 
    mà KHÔNG làm biến dạng tỉ lệ răng (Letterboxing với Padding).
    
    :param image: Ảnh gốc
    :param target_shape: (H, W) mục tiêu, ví dụ (640, 640)
    :param fill_value: Giá trị viền padding (0 = đen)
    :return: (Ảnh đã resize & pad, ratio_scale, (pad_w, pad_h))
    """
    h_orig, w_orig = image.shape[:2]
    target_h, target_w = target_shape

    # Tính tỉ lệ scale giữ nguyên aspect ratio
    r = min(target_w / w_orig, target_h / h_orig)
    new_w, new_h = int(round(w_orig * r)), int(round(h_orig * r))

    # Resize ảnh
    resized = cv2.resize(image, (new_w, new_h), interpolation=cv2.INTER_AREA)

    # Tính padding
    pad_w = (target_w - new_w) // 2
    pad_h = (target_h - new_h) // 2

    # Thêm padding
    if len(image.shape) == 3:
        padded = np.full((target_h, target_w, 3), fill_value, dtype=image.dtype)
        padded[pad_h:pad_h + new_h, pad_w:pad_w + new_w, :] = resized
    else:
        padded = np.full((target_h, target_w), fill_value, dtype=image.dtype)
        padded[pad_h:pad_h + new_h, pad_w:pad_w + new_w] = resized

    return padded, r, (pad_w, pad_h)


def compute_image_metrics(image: np.ndarray) -> Dict[str, float]:
    """
    Tính toán các chỉ số thống kê về độ sáng và độ tương phản của ảnh X-quang.
    
    :param image: Ảnh đầu vào
    :return: Dictionary chứa Mean, Std (Contrast), RMS Contrast
    """
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY) if len(image.shape) == 3 else image
    mean_val = float(np.mean(gray))
    std_val = float(np.std(gray))
    # RMS Contrast (Root Mean Square)
    normalized = gray.astype(np.float64) / 255.0
    rms_contrast = float(np.sqrt(np.mean((normalized - np.mean(normalized)) ** 2)))

    return {
        "mean_brightness": round(mean_val, 2),
        "std_contrast": round(std_val, 2),
        "rms_contrast": round(rms_contrast, 4)
    }


if __name__ == "__main__":
    print("=== Module Tiền xử lý ảnh X-Quang Nha Khoa đã sẵn sàng! ===")

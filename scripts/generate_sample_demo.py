"""
Script tạo ảnh thử nghiệm Panorama và chạy Demo Tiền xử lý OpenCV
Bài tập lớn: Hệ thống quản lý nha khoa tích hợp AI - Tuần 1
Sinh viên: Ngô Xuân Minh Hiếu
"""

import os
import sys

if sys.platform == "win32":
    sys.stdout.reconfigure(encoding="utf-8")
    sys.stderr.reconfigure(encoding="utf-8")

sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), "..")))

import cv2
import numpy as np
from src.preprocessing import apply_clahe, denoise_xray, letterbox_resize, compute_image_metrics
from src.eda_visualization import plot_preprocessing_comparison, draw_dental_annotations


def create_synthetic_panoramic_xray(width: int = 1200, height: int = 600) -> np.ndarray:
    """Tạo ảnh giả lập Panoramic X-ray với độ tương phản kém và nhiễu hạt để thử nghiệm OpenCV."""
    # Tạo nền xám x-quang
    img = np.full((height, width), 40, dtype=np.uint8)

    # Vẽ đường cong xương hàm (Mandible curve)
    center_x, center_y = width // 2, height // 2 + 50
    cv2.ellipse(img, (center_x, center_y), (450, 180), 0, 0, 180, 120, 25)

    # Vẽ các mầm/thân răng màu sáng (cường độ xám cao tượng trưng men răng)
    tooth_positions = []
    # Hàm trên
    for i in range(16):
        x = int(width * 0.15 + i * (width * 0.7 / 15))
        y = int(height * 0.42 - np.sin(i / 15 * np.pi) * 30)
        cv2.ellipse(img, (x, y), (18, 30), 0, 0, 360, 180, -1)
        cv2.ellipse(img, (x, y + 20), (10, 25), 0, 0, 360, 130, -1) # Chân răng
        tooth_positions.append((x, y))

    # Hàm dưới
    for i in range(16):
        x = int(width * 0.15 + i * (width * 0.7 / 15))
        y = int(height * 0.60 + np.sin(i / 15 * np.pi) * 30)
        cv2.ellipse(img, (x, y), (18, 30), 0, 0, 360, 180, -1)
        cv2.ellipse(img, (x, y - 20), (10, 25), 0, 0, 360, 130, -1) # Chân răng

    # Mô phỏng vết sâu răng (vùng khuyết/thấu quang - màu tối) trên răng 16 & 46
    cv2.circle(img, (tooth_positions[3][0] + 8, tooth_positions[3][1] - 5), 6, 60, -1)
    cv2.circle(img, (tooth_positions[12][0] - 8, tooth_positions[12][1] + 5), 7, 50, -1)

    # Thêm nhiễu Gaussian và nhiễu hạt X-quang
    noise = np.random.normal(0, 12, (height, width)).astype(np.float32)
    noisy_img = np.clip(img.astype(np.float32) + noise, 0, 255).astype(np.uint8)

    # Làm mờ độ tương phản tổng thể (tương tự X-quang chất lượng kém)
    blurred_low_contrast = cv2.convertScaleAbs(noisy_img, alpha=0.7, beta=20)
    return blurred_low_contrast


def main():
    os.makedirs("data/samples", exist_ok=True)
    print("--- 1. Tạo ảnh giả lập Panorama X-Quang ---")
    raw_xray = create_synthetic_panoramic_xray()
    sample_path = "data/samples/sample_panoramic_xray.png"
    cv2.imwrite(sample_path, raw_xray)
    print(f"Đã lưu ảnh mẫu tại: {sample_path}")

    print("\n--- 2. Thực thi Pipeline Tiền xử lý OpenCV ---")
    clahe_xray = apply_clahe(raw_xray, clip_limit=3.0)
    denoised_xray = denoise_xray(clahe_xray, h=8.0)

    # Tính toán các chỉ số thống kê
    raw_metrics = compute_image_metrics(raw_xray)
    clahe_metrics = compute_image_metrics(clahe_xray)
    denoise_metrics = compute_image_metrics(denoised_xray)

    print(f"Ảnh gốc       -> RMS Contrast: {raw_metrics['rms_contrast']}, Std: {raw_metrics['std_contrast']}")
    print(f"Sau CLAHE     -> RMS Contrast: {clahe_metrics['rms_contrast']}, Std: {clahe_metrics['std_contrast']}")
    print(f"Sau Denoising -> RMS Contrast: {denoise_metrics['rms_contrast']}, Std: {denoise_metrics['std_contrast']}")

    print("\n--- 3. Trực quan hóa và Lưu biểu đồ so sánh ---")
    comparison_output = "data/samples/preprocessing_comparison.png"
    plot_preprocessing_comparison(raw_xray, clahe_xray, denoised_xray, save_path=comparison_output)

    print("\n--- 4. Minh họa Overlaid Nhãn Bệnh Lý & Đánh số răng FDI ---")
    # Mẫu nhãn đính kèm
    annotations = [
        {"box": [380, 220, 430, 280], "label": "Caries", "fdi_number": 14, "confidence": 0.94},
        {"box": [740, 210, 790, 270], "label": "Caries", "fdi_number": 25, "confidence": 0.89},
        {"box": [160, 310, 220, 390], "label": "Impacted Wisdom", "fdi_number": 48, "confidence": 0.96},
        {"box": [950, 310, 1010, 390], "label": "Impacted Wisdom", "fdi_number": 38, "confidence": 0.91}
    ]
    annotated_img = draw_dental_annotations(denoised_xray, annotations)
    annotated_output = "data/samples/annotated_panoramic_demo.png"
    cv2.imwrite(annotated_output, annotated_img)
    print(f"Đã lưu ảnh gán nhãn bệnh lý tại: {annotated_output}")

    print("\n=== Đã hoàn thành toàn bộ quá trình thử nghiệm minh họa OpenCV! ===")


if __name__ == "__main__":
    main()

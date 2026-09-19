"""
Module Trực quan hóa Dữ liệu (Exploratory Data Analysis & Visualization)
Hỗ trợ hiển thị ảnh X-quang, so sánh kết quả tiền xử lý OpenCV và trực quan hóa nhãn (Bounding box, FDI tooth number).

Tác giả: Ngô Xuân Minh Hiếu
Bài tập lớn: Hệ thống quản lý nha khoa tích hợp AI - Tuần 1
"""

import cv2
import numpy as np
import matplotlib.pyplot as plt
from typing import List, Dict, Tuple, Optional


# Palette màu sắc đẹp mắt cho các loại bệnh lý nha khoa
DISEASE_COLOR_MAP = {
    "Caries": (0, 0, 255),            # Đỏ: Sâu răng
    "Deep Caries": (0, 75, 200),       # Cam đỏ: Sâu răng sâu
    "Impacted Wisdom": (255, 0, 0),    # Xanh dương: Răng khôn mọc lệch
    "Periapical Lesion": (0, 255, 255), # Vàng: Viêm cuống / áp xe
    "FDI Tooth": (0, 255, 0)           # Xanh lá: Nhãn đánh số răng
}


def plot_preprocessing_comparison(original_img: np.ndarray, clahe_img: np.ndarray, denoise_img: np.ndarray, save_path: Optional[str] = None):
    """
    Vẽ so sánh giữa ảnh gốc, ảnh sau xử lý CLAHE và ảnh sau khử nhiễu.
    """
    fig, axes = plt.subplots(2, 3, figsize=(16, 8))
    fig.suptitle("So sánh Kỹ thuật Tiền xử lý Ảnh X-Quang Panorama Nha Khoa (OpenCV)", fontsize=14, fontweight='bold')

    # Hàng 1: Hiển thị ảnh
    axes[0, 0].imshow(original_img, cmap='gray')
    axes[0, 0].set_title("1. Ảnh X-quang Gốc")
    axes[0, 0].axis('off')

    axes[0, 1].imshow(clahe_img, cmap='gray')
    axes[0, 1].set_title("2. Ảnh sau CLAHE (Tăng tương phản)")
    axes[0, 1].axis('off')

    axes[0, 2].imshow(denoise_img, cmap='gray')
    axes[0, 2].set_title("3. Ảnh sau CLAHE + Fast NLM Denoising")
    axes[0, 2].axis('off')

    # Hàng 2: Hiển thị Histogram độ sáng
    axes[1, 0].hist(original_img.ravel(), 256, [0, 256], color='gray')
    axes[1, 0].set_title("Histogram Ảnh Gốc")
    axes[1, 0].set_xlim([0, 256])

    axes[1, 1].hist(clahe_img.ravel(), 256, [0, 256], color='blue', alpha=0.7)
    axes[1, 1].set_title("Histogram Sau CLAHE")
    axes[1, 1].set_xlim([0, 256])

    axes[1, 2].hist(denoise_img.ravel(), 256, [0, 256], color='green', alpha=0.7)
    axes[1, 2].set_title("Histogram Sau Denoising")
    axes[1, 2].set_xlim([0, 256])

    plt.tight_layout()
    if save_path:
        plt.savefig(save_path, dpi=300, bbox_inches='tight')
        print(f"Đã lưu biểu đồ so sánh tại: {save_path}")
    else:
        plt.show()
    plt.close()


def draw_dental_annotations(
    image: np.ndarray, 
    annotations: List[Dict[str, Any]]
) -> np.ndarray:
    """
    Vẽ các bounding box bệnh lý và nhãn số răng FDI lên ảnh X-quang.
    
    :param image: Ảnh X-quang BGR hoặc Grayscale
    :param annotations: Danh sách dict [{'box': [xmin, ymin, xmax, ymax], 'label': 'Caries', 'fdi_number': 16, 'confidence': 0.92}]
    :return: Ảnh đã overlay nhãn
    """
    output_img = image.copy()
    if len(output_img.shape) == 2:
        output_img = cv2.cvtColor(output_img, cv2.COLOR_GRAY2BGR)

    for ann in annotations:
        box = ann.get('box', [0, 0, 0, 0])
        label = ann.get('label', 'Dental Condition')
        fdi_num = ann.get('fdi_number', None)
        conf = ann.get('confidence', None)

        xmin, ymin, xmax, ymax = map(int, box)
        color = DISEASE_COLOR_MAP.get(label, (255, 255, 0))

        # Vẽ bounding box
        cv2.rectangle(output_img, (xmin, ymin), (xmax, ymax), color, 2)

        # Xây dựng text hiển thị
        display_text = f"{label}"
        if fdi_num is not None:
            display_text = f"R{fdi_num}: {label}"
        if conf is not None:
            display_text += f" ({conf:.0%})"

        # Vẽ khung nền chữ
        (w, h), baseline = cv2.getTextSize(display_text, cv2.FONT_HERSHEY_SIMPLEX, 0.5, 1)
        cv2.rectangle(output_img, (xmin, ymin - h - 6), (xmin + w, ymin), color, -1)
        cv2.putText(output_img, display_text, (xmin, ymin - 4), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (255, 255, 255), 1, cv2.LINE_AA)

    return output_img


if __name__ == "__main__":
    print("=== Module Trực quan hóa Dữ liệu Nha Khoa đã sẵn sàng! ===")

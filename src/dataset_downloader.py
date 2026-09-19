"""
Module Hướng dẫn & Tải Dữ liệu X-Quang Nha Khoa (Dataset Downloader)
Sử dụng KaggleHub và Roboflow API để tải các tập dữ liệu ảnh Panorama Nha Khoa công khai.

Tác giả: Ngô Xuân Minh Hiếu
Bài tập lớn: Hệ thống quản lý nha khoa tích hợp AI - Tuần 1
"""

import os
import sys


def print_dataset_sources_summary():
    """Hiển thị danh sách các nguồn dataset chuẩn cho bài toán nha khoa AI."""
    summary = """
================================================================================
          DANH SÁCH TẬP DỮ LIỆU ẢNH X-QUANG NHA KHOA (DENTAL PANORAMIC)
================================================================================

1. DENTEX Benchmark Dataset (MICCAI Challenge):
   - Mô tả: Tập dữ liệu ảnh Panorama gán nhãn răng chuẩn FDI + 4 loại bệnh lý.
   - Nguồn: https://github.com/ibrahimethemhamamci/DENTEX
   - Phù hợp: Đánh số răng FDI, Chẩn đoán sâu răng, Răng khôn, Viêm cuống.

2. Roboflow Universe Dental Datasets:
   - Dự án: Dental X-Ray Panoramic Dataset
   - Định dạng: YOLOv8 PyTorch / COCO JSON / Pascal VOC.
   - Nguồn: https://universe.roboflow.com/
   - Phù hợp: Chấn đoán sâu răng (Caries), Răng khôn mọc lệch (Impacted Tooth).

3. Kaggle Datasets:
   - Adult Caries Detection Dataset
   - Children's Dental Panoramic X-Ray Dataset (14 loại nhãn nha khoa)
   - Panoramic Dental Xray Dataset (Segmentation răng & xương hàm)
================================================================================
    """
    print(summary)


def download_from_roboflow(api_key: str, workspace: str, project: str, version: int, download_dir: str = "./data"):
    """
    Tải tập dữ liệu từ Roboflow Universe thông qua Roboflow SDK.
    
    :param api_key: Khóa API Roboflow cá nhân
    :param workspace: Tên workspace trên Roboflow
    :param project: Tên dự án
    :param version: Phiên bản dataset
    """
    try:
        from roboflow import Roboflow
        rf = Roboflow(api_key=api_key)
        rf_project = rf.workspace(workspace).project(project)
        dataset = rf_project.version(version).download("yolov8", location=download_dir)
        print(f"Đã tải thành công dataset từ Roboflow về thư mục: {dataset.location}")
        return dataset.location
    except ImportError:
        print("Chưa cài đặt gói 'roboflow'. Hãy chạy: pip install roboflow")
    except Exception as e:
        print(f"Lỗi khi tải từ Roboflow: {e}")


def download_from_kaggle(dataset_handle: str):
    """
    Tải tập dữ liệu từ Kaggle thông qua kagglehub.
    Ví dụ dataset_handle: "linxiaojie/childrens-dental-panoramic-x-ray-dataset"
    """
    try:
        import kagglehub
        path = kagglehub.dataset_download(dataset_handle)
        print(f"Đã tải thành công tập dữ liệu Kaggle về: {path}")
        return path
    except ImportError:
        print("Chưa cài đặt gói 'kagglehub'. Hãy chạy: pip install kagglehub")
    except Exception as e:
        print(f"Lỗi khi tải từ Kaggle: {e}")


if __name__ == "__main__":
    if sys.platform == "win32":
        sys.stdout.reconfigure(encoding="utf-8")
    print_dataset_sources_summary()

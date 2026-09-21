import os
import sys

# Thiết lập UTF-8 cho console Windows
if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from pipeline import CustomerServicePipeline

def run_tests():
    print("=" * 60)
    print(" BẮT ĐẦU KIỂM THỬ TỰ ĐỘNG PIPELINE ENGINE CHATBOT AI")
    print("=" * 60)

    pipeline = CustomerServicePipeline()

    test_queries = [
        "Xin chào, phòng khám tư vấn giúp tôi",
        "Giá cạo vôi răng và trám răng bao nhiêu tiền?",
        "Bác sĩ Chỉnh nha niềng răng tại phòng khám là ai?",
        "Tôi là Nguyễn Văn A, SĐT 0912345678 muốn đặt lịch hẹn niềng răng vào sáng mai",
        "Địa chỉ phòng khám ở đâu và làm việc đến mấy giờ?"
    ]

    for i, q in enumerate(test_queries, 1):
        print(f"\n--- TEST CASE {i}: '{q}' ---")
        answer, logs = pipeline.run_pipeline(q)
        print("\n[LOGS PHÂN TÍCH PHÂN CẤP]:")
        for log in logs:
            print(f"  • {log}")
        print("\n[KẾT QUẢ CÂU TRẢ LỜI CỦA CHATBOT]:")
        print(answer)
        print("-" * 60)

if __name__ == "__main__":
    run_tests()

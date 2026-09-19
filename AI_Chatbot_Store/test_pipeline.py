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
        "Cho tôi xem laptop card RTX dưới 20 triệu",
        "Tôi muốn mua 2 chiếc Lenovo LOQ P01 thì hết bao nhiêu tiền?",
        "Chính sách đổi trả và bảo hành sản phẩm của cửa hàng như thế nào?"
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

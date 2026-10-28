"""
Script & Hướng Dẫn Huấn Luyện Tinh Chỉnh (Fine-Tuning) Mô Hình LLM Nha Khoa
Hệ thống Quản lý Nha khoa Tích hợp AI
Sinh viên thực hiện: Ngô Xuân Minh Hiếu

Script này cung cấp 3 phương án huấn luyện tinh chỉnh mô hình ngôn ngữ lớn (LLM):
1. Phương án A: Fine-tuning Open-Source LLMs (Qwen2.5 / Llama-3 / Gemma-2) bằng LoRA / QLoRA với Unsloth (Miễn phí trên Colab T4/A100).
2. Phương án B: Fine-tuning qua OpenAI API (gpt-4o-mini-2024-07-18).
3. Phương án C: Fine-tuning qua Google Gemini Tuning API.
"""

import os
import sys
import json

if sys.platform == "win32":
    sys.stdout.reconfigure(encoding="utf-8")

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DATA_DIR = os.path.join(BASE_DIR, "data")
DATASET_PATH = os.path.join(DATA_DIR, "dental_sft_dataset.jsonl")


def print_fine_tune_architecture_overview():
    print("""
================================================================================
  QUY TRÌNH HUẤN LUYỆN TINH CHỈNH (SUPERVISED FINE-TUNING - SFT) CHO NHA KHOA
================================================================================
1. TẬP DỮ LIỆU HUẤN LUYỆN (Dataset):
   - Đã tạo tại: data/dental_sft_dataset.jsonl
   - Cấu trúc: Chuẩn tin nhắn Chat Completion đa lượt (System, User, Assistant).
   - Nội dung: Tri thức chuyên sâu bệnh học răng hàm mặt, cấy ghép implant, niềng răng,
     tiểu phẫu răng khôn, phục hình răng sứ và dược lý nha khoa.

2. BA LỰA CHỌN HUẤN LUYỆN:
   [LỰA CHỌN 1]: Huấn luyện mô hình mã nguồn mở bằng LoRA / QLoRA (Unsloth)
     - Mô hình khuyên dùng: Qwen/Qwen2.5-7B-Instruct hoặc meta-llama/Llama-3.1-8B-Instruct
     - Ưu điểm: Toàn quyền sở hữu mô hình (Weights), chạy On-Premise hoặc Server riêng tư, bảo mật 100%.
     - Chi phí: 0đ (Có thể chạy trên Google Colab Free với GPU T4 16GB).

   [LỰA CHỌN 2]: Fine-tuning qua OpenAI API
     - Mô hình: gpt-4o-mini
     - Lệnh chạy: python scripts/fine_tune_llm.py --provider openai

   [LỰA CHỌN 3]: Fine-tuning qua Google Gemini API
     - Mô hình: gemini-1.5-flash / gemini-2.0
     - Lệnh chạy: python scripts/fine_tune_llm.py --provider gemini
================================================================================
""")


def get_unsloth_colab_notebook_code() -> str:
    """Trả về code Python chuẩn để dán vào Google Colab chạy Unsloth Fine-tuning."""
    return '''# ==============================================================================
# UNSLOTH FINE-TUNING CHO NHA KHOA AI ASSISTANT (CHẠY TRÊN GOOGLE COLAB GPU T4)
# ==============================================================================

# 1. Cài đặt thư viện Unsloth siêu tốc (tăng tốc x5 lần, tiết kiệm 70% VRAM)
!pip install "unsloth[colab-new] @ git+https://github.com/unslothai/unsloth.git"
!pip install --no-deps trl peft accelerate bitsandbytes

# 2. Khởi tạo mô hình nền tảng (Qwen2.5-7B hoặc Llama-3-8B)
from unsloth import FastLanguageModel
import torch

max_seq_length = 2048
dtype = None # Auto detect (Float16 hoặc Bfloat16)
load_in_4bit = True # Bật lượng tử hóa 4-bit (QLoRA)

model, tokenizer = FastLanguageModel.from_pretrained(
    model_name = "unsloth/Qwen2.5-7B-Instruct-bnb-4bit",
    max_seq_length = max_seq_length,
    dtype = dtype,
    load_in_4bit = load_in_4bit,
)

# 3. Gắn Adapter LoRA để chỉ huấn luyện < 1% trọng số mô hình
model = FastLanguageModel.get_peft_model(
    model,
    r = 16, # LoRA Rank
    target_modules = ["q_proj", "k_proj", "v_proj", "o_proj", "gate_proj", "up_proj", "down_proj"],
    lora_alpha = 16,
    lora_dropout = 0,
    bias = "none",
    use_gradient_checkpointing = "unsloth",
    random_state = 3407,
)

# 4. Tải file dataset dental_sft_dataset.jsonl đã tạo trong project
from datasets import load_dataset
dataset = load_dataset("json", data_files="dental_sft_dataset.jsonl", split="train")

def formatting_prompts_func(examples):
    convos = examples["messages"]
    texts = [tokenizer.apply_chat_template(convo, tokenize = False, add_generation_prompt = False) for convo in convos]
    return { "text" : texts }

dataset = dataset.map(formatting_prompts_func, batched = True)

# 5. Huấn luyện SFT Trainer
from trl import SFTTrainer
from transformers import TrainingArguments

trainer = SFTTrainer(
    model = model,
    tokenizer = tokenizer,
    train_dataset = dataset,
    dataset_text_field = "text",
    max_seq_length = max_seq_length,
    dataset_num_proc = 2,
    packing = False,
    args = TrainingArguments(
        per_device_train_batch_size = 2,
        gradient_accumulation_steps = 4,
        warmup_steps = 5,
        max_steps = 60,
        learning_rate = 2e-4,
        fp16 = not torch.cuda.is_bf16_supported(),
        bf16 = torch.cuda.is_bf16_supported(),
        logging_steps = 1,
        optim = "adamw_8bit",
        weight_decay = 0.01,
        lr_scheduler_type = "linear",
        seed = 3407,
        output_dir = "outputs_dental_model",
    ),
)

trainer_stats = trainer.train()

# 6. Lưu mô hình đã fine-tune (GGUF để chạy Ollama hoặc LoRA Adapter)
model.save_pretrained_merged("dental_ai_qwen_model", tokenizer, save_method = "merged_16bit")
print("Đã hoàn tất huấn luyện và xuất mô hình Nha Khoa AI thành công!")
'''


def run_openai_finetune(api_key: str = None):
    """Gửi job fine-tuning lên OpenAI API."""
    try:
        from openai import OpenAI
        client = OpenAI(api_key=api_key or os.getenv("OPENAI_API_KEY"))

        if not os.path.exists(DATASET_PATH):
            print(f"Lỗi: Không tìm thấy file {DATASET_PATH}")
            return

        print(f"[OpenAI Fine-Tuning] Đang upload file {DATASET_PATH}...")
        with open(DATASET_PATH, "rb") as f:
            uploaded_file = client.files.create(file=f, purpose="fine-tune")

        file_id = uploaded_file.id
        print(f"[OpenAI Fine-Tuning] Đã upload thành công! File ID: {file_id}")

        print("[OpenAI Fine-Tuning] Đang khởi tạo Fine-Tuning Job cho model gpt-4o-mini-2024-07-18...")
        job = client.fine_tuning.jobs.create(
            training_file=file_id,
            model="gpt-4o-mini-2024-07-18",
            suffix="dental-clinic-premier"
        )
        print(f"[OpenAI Fine-Tuning] Job đã khởi tạo thành công! Job ID: {job.id}")
        print("Trạng thái:", job.status)
        return job
    except Exception as e:
        print(f"[OpenAI Fine-Tuning] Lỗi: {e}")


def export_colab_notebook_file():
    """Tạo file Colab notebook hoặc script python huấn luyện cục bộ."""
    colab_script_path = os.path.join(DATA_DIR, "train_dental_unsloth_colab.py")
    with open(colab_script_path, "w", encoding="utf-8") as f:
        f.write(get_unsloth_colab_notebook_code())
    print(f"[Fine-Tune Exporter] Đã lưu mã nguồn huấn luyện Colab Unsloth tại: {colab_script_path}")


if __name__ == "__main__":
    print_fine_tune_architecture_overview()
    export_colab_notebook_file()

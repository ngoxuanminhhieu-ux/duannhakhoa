# ==============================================================================
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

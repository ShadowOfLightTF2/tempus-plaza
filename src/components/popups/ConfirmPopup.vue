<template>
  <Teleport to="body">
    <Transition name="confirm-fade">
      <div
        v-if="modelValue"
        class="confirm-overlay"
        @click.self="onCancel"
        @keydown.esc="onCancel"
      >
        <div class="confirm-dialog" role="alertdialog" aria-modal="true">
          <h5 class="confirm-title">{{ title }}</h5>
          <p class="confirm-message">{{ message }}</p>
          <div class="confirm-actions">
            <button class="confirm-btn confirm-btn-cancel" @click="onCancel">
              {{ cancelText }}
            </button>
            <button class="confirm-btn confirm-btn-confirm" @click="onConfirm">
              {{ confirmText }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script>
export default {
  name: "ConfirmPopup",
  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default: "Are you sure?",
    },
    message: {
      type: String,
      default: "This action cannot be undone.",
    },
    confirmText: {
      type: String,
      default: "Confirm",
    },
    cancelText: {
      type: String,
      default: "Cancel",
    },
  },
  emits: ["update:modelValue", "confirm", "cancel"],
  watch: {
    modelValue(isOpen) {
      document.body.style.overflow = isOpen ? "hidden" : "";
    },
  },
  methods: {
    onConfirm() {
      this.$emit("confirm");
      this.$emit("update:modelValue", false);
    },
    onCancel() {
      this.$emit("cancel");
      this.$emit("update:modelValue", false);
    },
  },
};
</script>

<style scoped>
.confirm-overlay {
  position: fixed;
  inset: 0;
  z-index: 10050;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.55);
  backdrop-filter: blur(2px);
  -webkit-backdrop-filter: blur(2px);
}

.confirm-dialog {
  width: 100%;
  max-width: 360px;
  margin: 16px;
  background: rgba(24, 27, 34, 0.98);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  padding: 22px 22px 18px;
  box-shadow:
    0 20px 60px rgba(0, 0, 0, 0.6),
    0 0 0 1px rgba(255, 255, 255, 0.04);
}

.confirm-title {
  color: #ffffff;
  font-weight: bold;
  margin: 0 0 8px;
}

.confirm-message {
  color: rgba(255, 255, 255, 0.75);
  font-size: 0.92rem;
  margin: 0 0 20px;
  line-height: 1.4;
}

.confirm-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.confirm-btn {
  border: 2px solid transparent;
  border-radius: 10px;
  padding: 8px 16px;
  font-weight: bold;
  cursor: pointer;
  font-family: inherit;
  font-size: 0.9rem;
  transition:
    background 0.15s,
    border-color 0.15s;
}

.confirm-btn-cancel {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.05);
  border-color: #363a3d;
}
.confirm-btn-cancel:hover {
  background: rgba(255, 255, 255, 0.1);
}

.confirm-btn-confirm {
  color: #ffffff;
  background: rgba(220, 53, 69, 0.25);
  border-color: rgba(220, 53, 69, 0.6);
}
.confirm-btn-confirm:hover {
  background: rgba(220, 53, 69, 0.4);
}

.confirm-fade-enter-active,
.confirm-fade-leave-active {
  transition: opacity 0.15s ease;
}
.confirm-fade-enter-from,
.confirm-fade-leave-to {
  opacity: 0;
}
</style>

<template>
  <component
    :is="tag"
    :href="href"
    :to="to"
    @click="handleClick"
    @auxclick="handleAuxClick"
    @mousedown="handleMouseDown"
    v-bind="$attrs"
  >
    <slot></slot>
  </component>
</template>

<script>
export default {
  name: "SmartLink",

  props: {
    href: String,
    to: [String, Object],
    tag: {
      type: String,
      default: "a",
    },
  },

  methods: {
    handleClick(event) {
      if (event.ctrlKey || event.metaKey) {
        event.preventDefault();
        this.openInNewTab();
        return;
      }

      if (this.to) {
        event.preventDefault();
        this.$router.push(this.to);
        return;
      }

      this.$emit("click", event);
    },
    handleMouseDown(event) {
      if (event.button === 1) {
        event.preventDefault();
      }
    },
    handleAuxClick(event) {
      if (event.button === 1) {
        event.preventDefault();
        event.stopPropagation();
        this.openInNewTab();
      }
    },
    openInNewTab() {
      if (this.href) {
        window.open(this.href, "_blank");
      } else if (this.to) {
        const route = this.$router.resolve(this.to);
        window.open(route.href, "_blank");
      }
    },
  },
};
</script>

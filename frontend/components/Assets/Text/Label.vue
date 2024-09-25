<template>
  <small :class="colorClass">{{ label }}</small>
</template>

<script>
export default {
  props: {
    label: {
      required: true,
    },
    color: {
      type: String,
    },
  },
  computed: {
    colorClass() {
      return this.color ? `${this.color}--text` : "text-color";
    },
  },
};
</script>

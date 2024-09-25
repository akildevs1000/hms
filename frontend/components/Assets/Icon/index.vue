<template>
  <v-hover v-slot:default="{ hover, props }">
    <span v-bind="props">
      <v-icon
        :class="iconClass(hover)"
        class="px-2 pa-1"
        @click="click"
      >
        mdi-{{ icon }}
      </v-icon>
    </span>
  </v-hover>
</template>

<script>
export default {
  props: {
    icon: {
      type: String,
      required: true,
    },
  },
  methods: {
    click() {
      this.$emit("click");
    },
    iconClass(hover) {
      return hover
        ? "border-primary white--text primary"
        : "border-secondary";
    },
    spanClass(hover) {
      return hover ? "hovered-span" : "default-span";
    },
  },
};
</script>

<style scoped>
.border-secondary {
  border: 1px solid #ddd;
  border-radius: 3px;
}
.border-primary {
  border: 1px solid #4390FC;
  border-radius: 3px;
}
.default-span {
  display: inline-block;
  transition: all 0.3s ease;
}

.hovered-span {
  display: inline-block;
  transition: all 0.3s ease;
  transform: scale(1.1);
}

.default-icon {
  border: 1px solid #ddd;
  border-radius: 5px;
  transition: all 0.3s ease;
}

.hovered-icon {
  border: 1px solid #4390FC;
  border-radius: 5px;
  background-color: rgba(67, 144, 252, 0.1);
  box-shadow: 0px 4px 8px rgba(67, 144, 252, 0.3);
  transform: scale(1.1);
  transition: all 0.3s ease;
}
</style>

<template>
  <v-hover v-slot:default="{ hover, props }">
    <span v-bind="props">
      <div style="position: absolute; width: 100%">
        <span
          :style="{
            position: 'absolute',
            zIndex: 1,
            left: `${left}px`,
            left: computedLeft,
            top: `${top}px`,
          }"
        >
          <v-icon
            @click="$emit('click')"
            size="30"
            :class="iconClass(hover)"
            class="transition-icon"
            style="border-radius: 50px; width: 15px; height: 15px"
          >
            mdi-close-circle
          </v-icon>
        </span>
      </div>
    </span>
  </v-hover>
</template>

<script>
export default {
  props: {
    left: {
      default: 893,
    },
    top: {
      default: -13,
    },
  },
  computed: {
    computedLeft() {
      return typeof this.left === 'string' && this.left.includes('%')
        ? this.left // Use as is if it's a percentage
        : `${this.left}px`; // Convert to px if it's a number
    },
  },
  methods: {
    iconClass(hover) {
      return !hover ? "white--text grey" : "white primary--text";
    },
  },
};
</script>
<style scoped>
.transition-icon {
  transition: transform 0.3s ease-in-out;
}

/* Rotation Effect on Hover */
.transition-icon:hover {
  transform: rotate(180deg);
}
</style>
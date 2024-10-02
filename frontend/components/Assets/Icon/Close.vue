<template>
  <v-hover v-slot:default="{ hover, props }">
    <span v-bind="props">
      <div style="position: absolute; width: 100%">
        <span
          :style="{
            position: 'absolute',
            zIndex: 1,
            left: `${left}px`,
            top: `${top}px`,
          }"
        >
          <v-icon
            @click="$emit('click')"
            size="30"
            :class="iconClass(hover)"
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
      type: Number,
      default: 893,
    },
    top: {
      type: Number,
      default: -13,
    },
  },
  methods: {
    iconClass(hover) {
      return !hover ? "white--text grey" : "white primary--text";
    },
  },
};
</script>

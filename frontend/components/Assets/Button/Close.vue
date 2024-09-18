<template>
  <v-hover v-slot:default="{ hover, props }">
    <span v-bind="props">
      <v-icon
        @click="submitClose"
        :outlined="!hover"
        color="primary"
      >
        {{ hover ? `mdi-close-circle` : `mdi-close-circle-outline` }}
      </v-icon>
    </span>
  </v-hover>
</template>

<script>
export default {
  methods: {
    submitClose() {
      this.$emit("close");
    },
  },
};
</script>

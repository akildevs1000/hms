<template>
  <v-hover v-slot:default="{ hover, props }">
    <span v-bind="props">
      <v-btn
        small
        :outlined="!hover"
        rounded
        color="red"
        class="white--text"
        @click="submitClose"
        >Cancel</v-btn
      >
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


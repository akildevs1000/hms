<template>
  <v-hover v-slot:default="{ hover, props }">
    <span v-bind="props">
      <v-btn
        :disabled="isDisabled || false"
        x-small
        :outlined="!hover"
        rounded
        color="green"
        class="white--text"
        @click="submitClose"
        >Submit</v-btn
      >
    </span>
  </v-hover>
</template>

<script>
export default {
  props: ["isDisabled"],
  methods: {
    submitClose() {
      this.$emit("click");
    },
  },
};
</script>

<template>
  <v-hover v-slot:default="{ hover, props }">
    <span v-bind="props">
      <v-btn
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
  methods: {
    submitClose() {
      this.$emit("click");
    },
  },
};
</script>

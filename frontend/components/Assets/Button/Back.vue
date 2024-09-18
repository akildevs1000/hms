<template>
  <v-hover v-slot:default="{ hover, props }">
    <span v-bind="props">
      <v-btn
        x-small
        :outlined="!hover"
        :class="hover ? `white--text` : `primary--text`"
        rounded
        color="primary"
        @click="submitClose"
        ><v-icon small>mdi-chevron-left</v-icon>Back</v-btn
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

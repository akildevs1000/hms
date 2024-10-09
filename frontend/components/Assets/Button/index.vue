<template>
  <v-hover v-slot:default="{ hover, props }">
    <span v-bind="props">
      <v-btn
        x-small
        :outlined="!hover"
        rounded
        :color="options.color || 'green'"
        class="white--text"
        @click="$emit(`click`)"
        ><v-icon small>mdi-{{ options.icon }}</v-icon
        >{{ options.label || "Submit" }}</v-btn
      >
    </span>
  </v-hover>
</template>

<script>
export default {
  props: ["options"],
};
</script>

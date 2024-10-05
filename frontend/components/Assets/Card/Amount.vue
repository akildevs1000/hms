<template>
  <v-card rounded="lg" outlined class="px-4 pt-2">
    <v-row no-gutter>
      <v-col class="text-left mt-3">
        <p :class="`${options.color}--text`">
          <v-icon size="30" :color="options.color">{{ options.icon }}</v-icon>
          <br>
        </p>
      </v-col>
      <v-col class="text-right mt-3">
        <p :class="`${options.color}--text`">
          {{ options.value }}
          <br>
          <small :class="`${options.color}--text`" style="font-size: 11px !important;">{{ options.label }}</small>
        </p>
      </v-col>
    </v-row>
  </v-card>
</template>
<script>
export default {
  props: ["options"],
};
</script>

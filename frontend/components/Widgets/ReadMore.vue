<template>
  <v-dialog v-model="dialog" persistent max-width="400px">
    <AssetsIconClose left="390" @click="dialog = false" />

    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on"
        ><span v-if="text.length > textLength">
          {{ text.slice(0, 5) }}...<span class="blue--text">more</span>
        </span>
        <span v-else>
          {{ text }}
        </span></span
      >
    </template>

    <v-card>
      <v-alert class="grey lighten-3" flat dense>Chat</v-alert>
      <v-card-text>{{ text }}</v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    text: {
      type: String,
      required: true,
      default: "",
    },
    textLength: {
      default: 10,
    },
  },
  data: () => ({
    dialog: false,
  }),
};
</script>

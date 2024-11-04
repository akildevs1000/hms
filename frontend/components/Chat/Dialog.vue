<template>
  <v-dialog ref="chatDialog" v-model="dialog" persistent width="90%">
    <AssetsIconClose left="89.5%" @click="dialog = false" />

    <template v-slot:activator="{ on, attrs }">
      <v-btn
        v-bind="attrs"
        v-on="on"
        fixed
        small
        fab
        color="indigo"
        style="bottom: 5px; right: 1px"
        dark
        class="sticky-chat-button"
      >
        <v-icon small>mdi-message-text</v-icon>
      </v-btn>
    </template>

    <v-card style="overflow: hidden">
      <v-alert class="grey lighten-3" flat dense>Chat</v-alert>
      <v-card-text>
        <Chat />
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  layout: "empty",
  data: () => ({ dialog: false }),
};
</script>

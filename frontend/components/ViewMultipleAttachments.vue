<template>
  <v-dialog v-model="dialog" height="700" width="900">
    <template v-slot:activator="{ on, attrs }">
      <!-- <v-icon color="primary" v-bind="attrs" v-on="on">mdi-attachment</v-icon> -->
      <v-icon v-if="items.length" color="primary" v-bind="attrs" v-on="on"
        >mdi-paperclip</v-icon
      >
      <small class="mx-2">{{ items.length }}</small>
      <!-- <v-icon color="primary" v-bind="attrs" v-on="on">mdi-file-pdf</v-icon>
      <v-icon color="primary" v-bind="attrs" v-on="on">mdi-paperclip</v-icon> -->
    </template>

    <v-card flat>
      <v-card-title
        >{{ label }} <v-spacer></v-spacer>

        <v-icon @click="dialog = false" color="primary"
          >mdi-close-circle-outline</v-icon
        >
      </v-card-title>
      <v-container fluid>
        <v-row>
          <v-col v-for="(item, index) in items" :key="index">
            <v-card elevation="5">
              <div class="primary white--text pa-2">
                {{ item.slug || "---" }}
              </div>
              <v-img :src="`${item.attachment}`"></v-img>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
      default: "Attachments",
    },
    attachments: {
      type: Array,
      default: [],
    },
  },
  data: () => ({
    dialog: false,
    items: [],
  }),
  created() {
    this.items = this.attachments;
  },
};
</script>

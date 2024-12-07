<template>
  <v-dialog v-model="dialog" height="700" width="900">
    <AssetsIconClose left="890" @click="dialog = false" />
    <template v-slot:activator="{ on, attrs }">
      <v-icon v-if="items.length" color="primary" v-bind="attrs" v-on="on">
        mdi-paperclip
      </v-icon>
      <small class="mx-2">{{ items.length }}</small>
    </template>

    <v-card flat>
      <v-card-title>
        {{ label }}
        <v-spacer></v-spacer>
      </v-card-title>
      <v-container fluid>
        <v-row>
          <v-col cols="12" v-for="(item, index) in items" :key="index">
            <v-card elevation="5">
              <div class="primary white--text pa-2">File {{ index + 1 }}</div>

              <!-- Check the file type and display accordingly -->
              <template v-if="isImage(item)">
                <v-img :src="`${item.attachment}`" alt="Image Preview"></v-img>
              </template>

              <template v-else-if="isPDF(item)">
                <iframe
                  :src="`${item.attachment}`"
                  width="100%"
                  height="600px"
                ></iframe>
              </template>
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
  data() {
    return {
      dialog: false,
      items: [],
    };
  },
  created() {
    this.items = this.attachments;
  },
  methods: {
    // Method to check if the file is an image (PNG or JPEG)
    isImage(item) {
      const imageExtensions = ["png", "jpeg", "jpg"];
      const ext = item.attachment.split(".").pop().toLowerCase();
      return imageExtensions.includes(ext);
    },

    // Method to check if the file is a PDF
    isPDF(item) {
      const ext = item.attachment.split(".").pop().toLowerCase();
      return ext === "pdf";
    },
  },
};
</script>

<template>
  <v-card>
    <v-toolbar class="rounded-md" color="background" dense flat dark>
      <span>Add Document</span>
      <v-spacer></v-spacer>
    </v-toolbar>
    <v-container class="pa-5">
      <v-row>
        <v-col md="12">
          <!-- <client-only>
            <v-file-input
              counter
              outlined
              show-size
              truncate-length="15"
            ></v-file-input>
          </client-only> -->
          <client-only>
            <v-file-input
              v-model="files"
              color="deep-purple accent-4"
              counter
              label="File input"
              placeholder="Select your files"
              prepend-icon="mdi-paperclip"
              outlined
              :show-size="1000"
            >
              <template v-slot:selection="{ index, text }">
                <v-chip
                  v-if="index < 2"
                  color="deep-purple accent-4"
                  dark
                  label
                  small
                >
                  {{ text }}
                </v-chip>
                <span
                  v-else-if="index === 2"
                  class="text-overline grey--text text--darken-3 mx-2"
                >
                  +{{ files.length - 2 }} File(s)
                </span>
              </template>
            </v-file-input>
          </client-only>
        </v-col>
        <v-divider></v-divider>
        <v-col md="12">
          <v-btn
            small
            dark
            class="primary pt-4 pb-4"
            @click="store_document(1)"
          >
            Submit
            <v-icon right dark>mdi-file</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
    <v-card-actions> </v-card-actions>
  </v-card>
</template>

<script>
export default {
  data() {
    return {
      x: 0,
      y: 0,
      files: [],
      customer: {
        document: [],
      },
    };
  },

  methods: {
    store_document(id) {
      console.log(this.customer.document);
      let payload = new FormData();
      payload.append("document", this.files);
      // payload.append("document", this.customer.document);
      // payload.append("image", this.customer.image);
      payload.append("booking_id", id);
      this.$axios
        .post("/store_document_test", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.subLoad = false;
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>

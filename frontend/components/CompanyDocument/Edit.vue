<template>
  <v-dialog v-model="dialog" width="700">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-pencil </v-icon>
        Edit
      </div>
    </template>

    <v-card>
      <v-toolbar flat class="grey lighten-3" dense>
        Edit {{ model }} <v-spacer></v-spacer
        ><AssetsButtonClose @close="close" /></v-toolbar
      >

      <v-card-text class="py-5">
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field
                outlined
                dense
                hide-details
                v-model="payload.name"
                label="Title"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                outlined
                dense
                hide-details
                v-model="payload.description"
                label="description"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-file-input
                prepend-icon=""
                append-icon="mdi-paperclip"
                outlined
                dense
                hide-details
                v-model="payload.document"
                label="Document"
              ></v-file-input>
            </v-col>
            <v-col cols="12" v-if="errorResponse">
              <span class="red--text">{{ errorResponse }}</span>
            </v-col>
            <v-col cols="12" class="text-right">
              <v-btn small color="grey" class="white--text" dark @click="close">
                Close
              </v-btn>
              <v-btn
                :loading="loading"
                small
                color="blue"
                class="white--text"
                dark
                @click="submit"
              >
                Submit
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props: ["item", "endpoint", "model"],
  data() {
    return {
      payload: {
        name: "",
        description: "",
        document: "",
      },
      dialog: false,
      loading: false,
      successResponse: null,
      errorResponse: null,
    };
  },
  created() {
    this.payload = {
      name: this.item.name,
      description: this.item.description,
    };
  },
  methods: {
    close() {
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },
    async submit() {
      this.loading = true;
      let formData = new FormData();
      formData.append("name", this.payload.name);
      formData.append("description", this.payload.description);
      if (this.payload.document) {
        formData.append("document", this.payload.document);
      }
      try {
        await this.$axios.post(`${this.endpoint}/${this.item.id}`, formData);
        this.close();
        this.$emit("response", "Record has been updated");
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
  },
};
</script>

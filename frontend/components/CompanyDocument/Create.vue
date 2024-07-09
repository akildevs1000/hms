<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="500">
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          small
          color="blue"
          class="white--text"
          dark
          v-bind="attrs"
          v-on="on"
        >
          <v-icon color="white" small> mdi-plus </v-icon> {{ model }}
        </v-btn>
      </template>

      <v-card>
        <v-toolbar flat class="blue white--text" dense>
          Create {{ model }} <v-spacer></v-spacer
          ><v-icon @click="close" color="white">mdi-close</v-icon></v-toolbar
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
                <v-btn
                  small
                  color="grey"
                  class="white--text"
                  dark
                  @click="close"
                >
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
  </div>
</template>
<script>
export default {
  props: ["endpoint", "model"],

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
      product_categories: [],
    };
  },
  created() {
    this.getVendorCategory();
  },
  methods: {
    close() {
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },
    async getVendorCategory() {
      let { data } = await this.$axios.get(`${this.endpoint}-category-list`);

      this.product_categories = data;
    },
    async submit() {
      this.loading = true;
      try {
        let formData = new FormData();
        formData.append("company_id", this.$auth.user.company_id);
        formData.append("name", this.payload.name);
        formData.append("description", this.payload.description);
        if (this.payload.document) {
          formData.append("document", this.payload.document);
        }

        await this.$axios.post(this.endpoint, formData);
        this.close();
        this.$emit("response", "payload has been inserted");
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
  },
};
</script>

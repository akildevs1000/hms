<template>
  <v-dialog v-model="dialog" width="480">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-pencil </v-icon>
        Edit
      </div>
    </template>

    <v-card>
      <v-toolbar flat class="blue white--text" dense>
        Edit {{ model }} <v-spacer></v-spacer
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
                v-model="payload.title"
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
              <v-text-field
                type="number"
                min="0"
                outlined
                dense
                hide-details
                v-model="payload.unit_price"
                label="Unit Price"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-checkbox
                outlined
                dense
                hide-details
                v-model="payload.is_for_hall"
                label="Is this plan for Hall ?"
              ></v-checkbox>
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
        title: "",
        description: "",
        no_of_pax: 0,
        unit_price: "",
      },
      dialog: false,
      loading: false,
      successResponse: null,
      errorResponse: null,
    };
  },
  created() {
    this.payload = this.item;
  },
  methods: {
    close() {
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },
    async submit() {
      this.loading = true;
      try {
        await this.$axios.put(
          `${this.endpoint}/${this.payload.id}`,
          this.payload
        );
        this.close();
        this.$emit("response", "Record has been inserted");
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
  },
};
</script>

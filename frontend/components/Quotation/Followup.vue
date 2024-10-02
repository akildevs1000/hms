<template>
  <v-dialog v-model="dialog" width="600">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-clock-outline </v-icon>
        Follow Up
      </div>
    </template>

    <v-card>
      <v-toolbar flat class="grey lighten-3" dense>
        Follow Up
        {{ model }} <v-spacer></v-spacer
        ><AssetsButtonClose @close="close" /></v-toolbar
      >

      <v-card-text class="py-5">
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-textarea
                rows="3"
                hide-details
                dense
                outlined
                v-model="payload.description"
                label="Description"
              ></v-textarea>
            </v-col>
            <v-col cols="12">
              <v-select
                outlined
                dense
                hide-details
                v-model="payload.status"
                :items="[`Open`, `Close`]"
                label="Status"
              ></v-select>
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
  props: ["id", "endpoint", "model"],

  data() {
    return {
      book_date_menu: false,
      arrival_date_menu: false,
      departure_date_menu: false,

      payload: {
        description: ``,
        status: `Open`,
        id: 0,
      },
      dialog: false,
      loading: false,
      successResponse: null,
      errorResponse: null,
      customers: [],
      rooms: [],
      foodplans: [],
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
      this.payload.quotation_id = this.id;
      this.payload.user_id = this.$auth.user.id;
      try {
        await this.$axios.post(this.endpoint, this.payload);
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
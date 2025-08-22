<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="480">
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
        <v-toolbar flat class="grey lighten-3" dense>
          Create {{ model }} <v-spacer></v-spacer
          ><AssetsButtonClose @close="close"
        /></v-toolbar>

        <v-card-text class="py-5">
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  type="number"
                  min="0"
                  outlined
                  dense
                  hide-details
                  v-model="payload.number"
                  label="Floor Number"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  outlined
                  dense
                  hide-details
                  v-model="payload.name"
                  label="Name"
                ></v-text-field>
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
        name: null,
        number: null,
        company_id: this.$auth.user.company_id,
      },
      dialog: false,
      loading: false,
      successResponse: null,
      errorResponse: null,
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
      try {
        await this.$axios.post(this.endpoint, this.payload);
        this.close();
        this.$emit("response", "Floor has been added");
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
  },
};
</script>

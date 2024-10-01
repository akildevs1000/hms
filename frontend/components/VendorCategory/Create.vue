<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="500">
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          small
          color="primary"
          class="white--text"
          dark
          v-bind="attrs"
          v-on="on"
        >
          <v-icon color="white" small> mdi-plus </v-icon> New
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
                  outlined
                  dense
                  hide-details
                  v-model="payload.name"
                  label="Name"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-textarea
                  rows="2"
                  outlined
                  dense
                  hide-details
                  v-model="payload.description"
                  label="Description"
                ></v-textarea>
              </v-col>

              <v-col cols="12" v-if="errorResponse">
                <span class="red--text">{{ errorResponse }}</span>
              </v-col>
              <v-col cols="12" class="text-right">
                <AssetsButton
                  :options="{
                    label: `Cancel`,
                    color: `red`,
                  }"
                  @click="close"
                />
                &nbsp;
                <AssetsButton
                  :options="{
                    label: `Submit`,
                    color: `green`,
                  }"
                  @click="submit"
                />
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
      },
      dialog: false,
      loading: false,
      successResponse: null,
      errorResponse: null,
    };
  },
  created() {},
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
        this.$emit("response", "Record has been inserted");
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
  },
};
</script>

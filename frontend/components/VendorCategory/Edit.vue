<template>
  <v-dialog v-model="dialog" width="500">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="primary" small> mdi-pencil </v-icon>
        Edit
      </div>
    </template>

    <v-card>
      <v-toolbar flat class="grey lighten-3" dense>
        Edit {{ model }} <v-spacer></v-spacer><AssetsButtonClose @close="close"
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
</template>
<script>
export default {
  props: ["item", "endpoint", "model"],
  data() {
    return {
      payload: {
        name: "",
        description: "",
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

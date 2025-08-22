<template>
  <v-dialog v-model="dialog" width="480">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-eye </v-icon>
        View
      </div>
    </template>

    <v-card>
      <v-toolbar flat class="grey lighten-3" dense>
        View {{ model }} <v-spacer></v-spacer><AssetsButtonClose @close="close"
      /></v-toolbar>

      <v-card-text class="py-5">
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field
                outlined
                dense
                hide-details
                readonly
                v-model="payload.number"
                label="Number"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                outlined
                dense
                hide-details
                readonly
                v-model="payload.name"
                label="Name"
              ></v-text-field>
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
  created() {
    this.payload = this.item;
  },
  methods: {
    close() {
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },
  },
};
</script>

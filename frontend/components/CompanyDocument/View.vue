<template>
  <v-dialog v-model="dialog" width="700">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-eye </v-icon>
        View
      </div>
    </template>

    <v-card>
      <v-toolbar flat class="grey lighten-3" dense>
        View {{ model }} <v-spacer></v-spacer
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
                readonly
                v-model="payload.name"
                label="Title"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                outlined
                dense
                hide-details
                readonly
                v-model="payload.description"
                label="description"
              ></v-text-field>
            </v-col>
            <v-col cols="6" class="text-center">
              <v-avatar v-if="payload.path" tile size="450">
                <img :src="payload.path" />
              </v-avatar>
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
        no_of_pax: "",
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
  },
};
</script>

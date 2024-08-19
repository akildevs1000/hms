<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="900">
      <template v-slot:activator="{ on, attrs }">
        <v-btn block small color="primary" dark v-bind="attrs" v-on="on">
          ID <v-icon right>mdi-eye-outline</v-icon>
        </v-btn>
      </template>

      <v-card>
        <v-toolbar flat class="primary" dense dark>
          Picture and ID
          <v-spacer></v-spacer>
          <v-icon @click="close"> mdi-close </v-icon>
        </v-toolbar>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="6">
                <v-img :src="customer.captured_photo"></v-img>
              </v-col>
              <v-col cols="6">
                <v-card outlined style="min-height: 400px">
                  <v-img :src="customer.sign"></v-img>
                </v-card>
              </v-col>
              <v-col cols="6">
                <v-img :src="customer.id_frontend_side"></v-img>
              </v-col>
              <v-col cols="6">
                <v-img :src="customer.id_backend_side"></v-img>
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
  props: ["BookingId"],
  data() {
    return {
      endpoint: "https://backend.myhotel2cloud.com/api",
    //   endpoint: "https://hms-backend.test/api",

      dialog: false,
      customer: {},
    };
  },
  async created() {
    await this.getData(this.BookingId);
  },
  methods: {
    async getData(BookingId = 1) {
      try {
        let endpoint = `${this.endpoint}/get-lattest-customer-info/${BookingId}`;
        let { data } = await this.$axios.get(endpoint);
        this.customer = data;
      } catch (error) {
        console.log(error);
      }
    },
    close(){
        this.$emit(`getCustomerDocs`, this.customer);
        this.dialog = false;
    }
  },
};
</script>

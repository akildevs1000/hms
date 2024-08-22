<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="1100">
      <template v-slot:activator="{ on, attrs }">
        <v-btn block small color="primary" dark v-bind="attrs" v-on="on">
          ID <v-icon right>mdi-camera-outline</v-icon>
        </v-btn>
      </template>

      <v-card>
        <v-toolbar flat class="primary" dense dark>
          Picture and ID
          <v-spacer></v-spacer>
          <v-icon @click="close"> mdi-close </v-icon>
        </v-toolbar>

        <v-card-text>
          <v-container v-if="isValid">
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
              <!-- <v-col cols="12">
                {{ url }}
              </v-col> -->
            </v-row>
          </v-container>
          <v-container v-else>
            <v-card outlined>
              <v-row style="min-height: 350px" align="center" justify="center">
                <v-col class="text-center"> No data found </v-col>
              </v-row>
            </v-card>
          </v-container>
          <v-container>
            <v-row>
              <v-col cols="6">
                <v-btn
                  :loading="reloadLoading"
                  class="primary"
                  block
                  @click="getData(BookingId)"
                >
                  <v-icon>mdi-reload</v-icon>
                </v-btn>
              </v-col>
              <v-col cols="6">
                <v-btn
                  :disabled="!isValid"
                  :loading="confirmLoading"
                  class="primary"
                  block
                  @click="confirm"
                >
                  Confirm
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
  props: ["BookingId"],
  data() {
    return {
      reloadLoading: false,
      confirmLoading: false,
      endpoint: "https://backend.myhotel2cloud.com/api",
      //   endpoint: "https://hms-backend.test/api",

      dialog: false,
      customer: null,
      url: null,

    };
  },
  async created() {
    await this.getData(this.BookingId);
  },
  computed:{
    isValid(){
      let customer = this.customer;
      if(!customer) {
        return false;
      }
      return customer.captured_photo && customer.sign && customer.id_frontend_side && customer.id_backend_side
    }
  },
  methods: {
    async getData(BookingId = 1) {
      this.reloadLoading = true;

      try {
        this.url = `${this.endpoint}/get-lattest-customer-info/${BookingId}`;
        let { data } = await this.$axios.get(this.url);
        this.customer = data;
        this.reloadLoading = false;
      } catch (error) {
        this.reloadLoading = false;
        console.log(error);
      }
    },
    async confirm() {
      this.confirmLoading = true;

      let payload = {
        booking_id: this.BookingId,
        customer_id: this.customer.id,
      };
      try {
        await this.$axios.post(`booking-verify`, payload);
        this.$emit(`getCustomerDocs`, this.customer);
        this.dialog = false;
        this.confirmLoading = false;
      } catch (error) {
        console.log(error);
        this.confirmLoading = false;
      }
    },
    close() {
      this.dialog = false;
    },
  },
};
</script>

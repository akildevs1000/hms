<template>
   <v-dialog v-model="idPreviewPopup" width="1100">
      <!-- <template v-slot:activator="{ on, attrs }">
        <v-btn block small color="primary" dark v-bind="attrs" v-on="on">
          ID <v-icon right>mdi-camera-outline</v-icon>
        </v-btn>
      </template> -->
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
</template>
<script>
export default {
  props: ["BookingId", "RoomId"],
  data() {
    return {
      reloadLoading: false,
      confirmLoading: false,
      endpoint: "https://backend.myhotel2cloud.com/api",
      //   endpoint: "https://hms-backend.test/api",
      idPreviewPopup: false,
      customer: null,
      url: null,
    };
  },
  async created() {
    await this.getData(this.BookingId);
  },
  computed: {
    isValid() {
      let customer = this.customer;
      if (!customer) {
        return false;
      }
      return (
        customer.captured_photo &&
        customer.sign &&
        customer.id_frontend_side &&
        customer.id_backend_side
      );
    },
  },
  methods: {
    async getData() {
      this.reloadLoading = true;

      try {
        this.url = `${this.endpoint}/get-verify-info/${this.$auth.user.company_id}`;
        let { data } = await this.$axios.get(this.url);
        this.customer = {
          captured_photo: data.captured_photo_url,
          sign: data.sign_url,
          id_frontend_side: data.id_frontend_side_url,
          id_backend_side: data.id_backend_side_url,
        };
        this.reloadLoading = false;
      } catch (error) {
        this.reloadLoading = false;
        console.log(error);
      }
    },
    async confirm() {
      this.confirmLoading = true;

      const payload = {
        ...this.customer,
        company_id: this.$auth.user.company_id,
      };

      try {
        const verifyCustomerUrl = `verify-customer/${this.BookingId}`;
        await this.$axios.post(verifyCustomerUrl, payload);

        // Proceed with direct check-in only after customer verification
        const checkInUrl = `direct_check_in_room`;
        await this.$axios.post(checkInUrl, {
          room_id: this.RoomId,
          booking_id: this.BookingId,
        });
        this.$emit(`response`);
        this.$swal("Success!", "Reservation created successfully", "success");
      } catch (error) {
        console.error(error);
      } finally {
        this.confirmLoading = false;
        this.idPreviewPopup = false;
      }
    },
    close() {
      this.idPreviewPopup = false;
    },
  },
};
</script>

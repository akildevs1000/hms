<template>
  <div class="text-center">
    <v-col cols="12" sm="12" md="12" lg="12" class="text-center">
      <v-card ref="form" v-if="whatsapp_number != ''" :loading="loading">
        <v-card-text>
          <p class="text-center">
            Sent a Whatsapp OTP to registered Mobile Number:
            {{ maskNumber(whatsapp_number) }}
          </p>

          <v-text-field
            ref="name"
            outlined
            v-model="whatsapp_otp"
            :rules="[() => !!name || 'This field is required']"
            :error-messages="errorMessages"
            label="Whatsapp OTP"
            placeholder="Whatsapp OTP"
            required
            type="number"
          ></v-text-field>
          <label style="color: red">{{ error_message }}</label>
        </v-card-text>
        <v-divider class="mt-12"></v-divider>
        <v-card-actions>
          <v-btn color="error" desne small @click="sendOTP()"> Resend </v-btn>
          <v-spacer></v-spacer>

          <v-btn color="primary" desne small @click="verifyOtp()">
            Verify OTP
          </v-btn>
        </v-card-actions>
      </v-card>
      <div v-else>Check-in Details are not Found. Please try again</div>
    </v-col>
  </div>
</template>

<script>
export default {
  layout: "qrcode",
  data: () => ({
    errorMessages: [],
    id: "",
    pageValid: true,
    whatsapp_otp: "",
    name: "",
    whatsapp_number: "",
    loading: false,
    customer_otp: "",
    error_message: "",
  }),
  auth: false,
  mounted() {
    localStorage.setItem("hotelQRCodeOTPverified", false);
    this.clearDefaults();
    // this.$nextTick(function () {
    //   console.log(
    //     "hotelQrcodeWhatsappNumber",
    //     this.$store.state.hotelQrcodeWhatsappNumber
    //   );

    //   this.whatsapp_number = this.maskNumber(
    //     this.$store.state.hotelQrcodeWhatsappNumber
    //   );

    //   if (this.$store.state.hotelQrcodeRoomNumber) {
    //   } else {
    //     this.pageValid = false;
    //   }
    // });
  },
  created() {
    this.$store.commit("hotelQRCodeOTPverified", false);

    this.id = this.$route.params.id;
    this.$store.commit("hotelQrcodeID", this.id);
    // this.whatsapp_number = this.maskNumber(
    //   this.$store.state.hotelQrcodeWhatsappNumber
    // );
    this.sendOTP();
  },
  methods: {
    sendOTP() {
      let IdArray = this.id.split("-");

      if (IdArray.length == 3) {
        this.getGuestDetails(IdArray[0], IdArray[2], IdArray[1]);
      }
    },
    clearDefaults() {
      localStorage.setItem("QRCodeCartItems", JSON.stringify([]));
    },
    getGuestDetails(company_id, roomId, roomNo) {
      let options = {
        params: {
          company_id: company_id,
          room_id: roomId,
          otp: 1,
        },
      };
      this.loading = true;
      this.$axios.get(`get_checkin_customer_data`, options).then(({ data }) => {
        if (data.check_in) {
          this.$store.commit("hotelQrcodeRequestId", this.id);
          this.$store.commit("hotelQrcodeCompanyId", company_id);
          this.$store.commit("hotelQrcodeRoomNumber", roomNo);
          this.$store.commit("hotelQrcodeRoomId", roomId);
          this.$store.commit(
            "hotelQrcodeWhatsappNumber",
            data.customer.whatsapp
          );
          this.loading = false;
          this.customer_otp = data.whatsapp_otp;
          localStorage.setItem("hotelQrcodeCompanyId", company_id);
          localStorage.setItem("hotelQrcodeRoomNumber", roomNo);
          localStorage.setItem("hotelQrcodeRoomId", roomId);
          localStorage.setItem("hotelQrcodeBookingId", data.booking_id);

          this.whatsapp_number = data.customer.whatsapp;
        } else if (data == "") {
          this.loading = false;
        }
      });
    },
    maskNumber(number) {
      if (number)
        return "X".repeat(Math.max(0, number.length - 5)) + number.slice(-4);
    },
    isPageValid() {
      if (this.$store.state.hotelQrcodeRoomNumber) {
        return true;
      } else {
        this.pageValid = false;
        return false;
      }
    },
    openPage(name) {
      this.$router.push("/qrcode/" + name);
    },

    verifyOtp() {
      console.log(this.whatsapp_otp, this.customer_otp);
      if (this.whatsapp_otp == this.customer_otp) {
        localStorage.setItem("hotelQRCodeOTPverified", true);
        this.$store.commit("hotelQRCodeOTPverified", true);

        localStorage.setItem("hotelQRCodeOTPverified", true);
        this.$router.push("/qrcode/home");
      } else {
        this.error_message = "Invalid OTP";
      }
    },
  },
};
</script>

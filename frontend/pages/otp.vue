<template>
  <v-container fill-height>
    <v-card class="mx-auto" width="1000" style="margin-top: 150px">
      <v-row no-gutters>
        <v-col>
          <v-card
            flat
            style="min-height: 500px; width: 100%"
            class="d-flex justify-center align-center pa-0 ma-0"
          >
            <v-card-text>
              <div class="text-center">
                <img style="width: 250px" :src="logo" alt="logo" />
              </div>
              <v-container>
                <div>
                  Hi there! It seems like you haven't subscribed to the telegram
                  channel yet. Please click the following link or scan the QR
                  code to subscribe by sending a "hi" message.
                </div>
                <br />
                <div class="text-center">
                  <v-otp-input v-model="otp" length="6"></v-otp-input>
                </div>
                <br />
                <div class="text-center">
                  <v-btn block small link color="primary" @click="validateOTP">
                    Submit
                  </v-btn>
                </div>
                <br />
                <div class="text-right">
                  <a small link color="primary" @click="backToLogin">
                    Back to login
                  </a>
                </div>
              </v-container>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="6" class="purple">
          <v-card
            dark
            flat
            style="min-height: 500px; width: 100%"
            class="white--text d-flex justify-center align-center pa-0 ma-0"
          >
            <v-card-text class="white--text">
              <h5>MyHotel2Cloud THE RIGHT SOLUTION FOR YOU</h5>
              <p>
                Make it simple, easy and accessible anywhere, anytime. Save
                time, stay compliant and reduce labor costs by streamlining how
                you collect hours worked and time-off accruals.
              </p>
            </v-card-text>
          </v-card>
        </v-col>
        <!-- <v-col><pre>{{ $auth.user.telegram_chat_id }}</pre></v-col> -->
      </v-row>
    </v-card>
  </v-container>
</template>

<script>
export default {
  layout: "login",
  data: () => ({
    logo: "/logo1.png",
    otp: null,
  }),
  async created() {
    await this.generateOTP();
  },
  methods: {
    async generateOTP() {
      // let botToken = `7356807670:AAGtb_m3juvOpUGZCBaMXK73oO7A0-iUPOg`;
      // let chatId = this.$auth.user.telegram_chat_id;
      let otp = Math.floor(100000 + Math.random() * 900000);
      const message = `Your OTP code is: ${otp}`;
      // let url = `https://api.telegram.org/bot${botToken}/sendMessage?chat_id=${chatId}&text=${message}`;
      console.log("🚀 ~ generateOTP ~ message:", message);
      if (this.$auth.user.mobile) {
        console.log(
          "🚀 ~ generateOTP ~ this.$auth.user.mobile:",
          this.$auth.user.mobile
        );
        try {
          await this.$axios.post(`/send-message`, {
            number: this.$auth.user.mobile,
            message: message,
          });
          this.storeOTP(otp);
        } catch (error) {
          console.error("Error sending message:", error);
        }
      }
    },
    async storeOTP(otp) {
      let user_id = this.$auth.user.id;
      let url = `/generate-telegram-otp/${user_id}`;
      let config = {
        params: { otp, expire_in_min: 1 },
      };
      try {
        const { data } = await this.$axios.get(url, config);
        console.log(data);
      } catch (error) {
        console.error("Error sending message:", error);
      }
    },

    async validateOTP() {
      let user_id = this.$auth.user.id;
      let url = `/validate-telegram-otp/${user_id}`;
      let config = {
        params: { otp: this.otp },
      };
      try {
        await this.$axios.get(url, config);
        this.$router.push(`/login`);
      } catch (error) {
        alert(error?.response?.data?.message);
      }
    },

    async backToLogin() {
      this.$auth.logout();
      this.$router.push(`/login`);
    },
  },
};
</script>

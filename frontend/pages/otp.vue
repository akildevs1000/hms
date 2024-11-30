<template>
  <v-app style="width: 100%">
    <div class="bg-body">
      <v-row style="height: 100%">
        <v-col
          style="padding: 0px; margin: auto"
          class="parent-login-body1111111"
        >
          <div
            class="login-page login-body-fixed222222 p-md-51111 mx-md-41111"
            style="
              padding: 20px !important;
              max-width: 80%;
              width: 300px;
              text-align: center;
              background-color: transparent;
              border: 0.5px solid #897ffb;
              border-radius: 10px;
              height: auto;
            "
          >
            <div style="min-height: 40px">
              <div style="width: 100%" class="text-center">
                <v-img
                  class="text-center"
                  style="
                    width: 180px;
                    padding: 0px;
                    margin: auto;
                    text-align: center;
                  "
                  src="/login/login-logo.png"
                ></v-img>
              </div>
            </div>
            <div style="font-size: 19px; padding-top: 5px; color: #5a6374">
              Hotel Management Software
            </div>
            <div
              style="
                font-size: 13px;
                padding-top: 5px;
                padding-bottom: 10px;
                color: #979ca2;
              "
            >
              AI Powered Cloud Software
            </div>
            <div style="color: #fff; text-align: left" class=" ">
              <v-row>
                <v-col md="12" class="text-center">
                  <v-form
                    style="max-width: 300px"
                    ref="form"
                    method="post"
                    v-model="valid"
                    ><div style="padding-bottom: 0px; text-align: left">
                      <label for="" style="font-size: 12px; color: black"
                        >OTP</label
                      >
                    </div>
                    <div class="form-outline mb-4 text-center">
                      <!-- <v-text-field
                        style="padding: 0px"
                        v-model="email"
                        :rules="emailRules"
                        hide-details
                        required
                        dense
                        outlined
                        type="email"
                      ></v-text-field> -->
                      <v-otp-input v-model="otp" length="6"></v-otp-input>
                    </div>

                    <div class="text-center pt-2 pb-2">
                      <span
                        v-if="msg"
                        class="error--text"
                        style="font-size: 10px; float: left"
                      >
                        {{ msg }}
                      </span>
                      <v-btn
                        style="
                          width: 100%;
                          background-color: #006a37;
                          color: #fff;
                        "
                        :loading="loading"
                        @click="validateOTP"
                        class="btn btn-primary btn-block text-white fa-lg mt-1 mb-3"
                      >
                        Submit
                      </v-btn>
                    </div>
                    <div
                      style="
                        margin: auto;
                        width: 100%;
                        text-align: center;
                        color: #979ca2;
                        font-size: 13px;
                      "
                    >
                      <span> Did not revieved otp? </span>
                      <span
                        style="cursor: pointer; color: cornflowerblue"
                        @click="generateOTP"
                        >Click to Resend</span
                      >
                    </div>

                    <hr class="hr-text gradient" data-content="or" />

                    <div
                      class="mt-5"
                      style="margin: auto; width: 100%; text-align: center"
                    >
                      <span class="pa-4">
                        <img
                          src="/login/facebook.png"
                          style="width: 30px; height: 30px"
                        />
                      </span>
                      <span class="pa-4">
                        <img
                          src="/login/gmail.png"
                          style="width: 30px; height: 30px"
                        /> </span
                      ><span class="pa-4">
                        <img
                          src="/login/twitter.png"
                          style="width: 30px; height: 30px"
                        />
                      </span>
                    </div>
                    <div
                      class="d-flex align-items-center justify-content-center pb-0"
                    ></div>
                  </v-form>
                </v-col>
              </v-row>
            </div>
          </div>
        </v-col>
      </v-row>
    </div>
  </v-app>
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
      this.storeOTP(otp);

      alert(message);
      // let url = `https://api.telegram.org/bot${botToken}/sendMessage?chat_id=${chatId}&text=${message}`;
      console.log("🚀 ~ generateOTP ~ message:", message);
      if (this.$auth.user.mobile) {
        console.log(
          "🚀 ~ generateOTP ~ this.$auth.user.mobile:",
          this.$auth.user.mobile
        );
        // try {
        //   await this.$axios.post(`/send-message`, {
        //     number: this.$auth.user.mobile,
        //     message: message,
        //   });
        //   this.storeOTP(otp);
        // } catch (error) {
        //   console.error("Error sending message:", error);
        // }
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

<style scoped>
.emailtext-field .v-input__slot {
  padding: 0 2px !important;
}
.emailtext-field .v-input__slot {
  min-height: 30px !important;
}
.emailtext-field .v-label {
  line-height: 11px !important;
}
.emailtext-field .v-input__icon {
  height: 17px !important;
}
* {
  margin: 0;
  padding: 0;
}

body,
html {
  height: 100%;
}

.bg-body {
  background-image: url("../static/login/login-bg2.png") !important;
  /* padding-top: 5%; */
  height: 100%;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}

.about-content {
  padding-left: 30%;
  padding-top: 1%;
  color: #fff;
  padding-right: 15%;
}

.btntext {
  color: #6946dd;
  font-weight: bold;
  font-size: 22px;
}

.login-body-fixed {
  position: fixed;
  right: 10%;
  top: 10%;
}

.login-page {
  margin-right: 10%;
  margin-top: -3%;
  /* background-color: #fff !important; */
  float: right;
}
@media (min-width: 1000px) and (max-width: 1400px) {
  .login-page {
    margin-right: 10%;
    margin-top: -6%;
    /* background-color: #fff !important; */
    float: right;
  }
}
@media (min-width: 1400px) and (max-width: 1900px) {
  .login-page {
    margin-right: 10%;
    margin-top: -6%;
    /* background-color: #fff !important; */
    float: right;
  }
}

@media (min-width: 1900px) {
  .login-page {
    margin-right: 10%;
    margin-top: -6%;

    float: right;
  }
}

/* Mobile */
@media (max-width: 500px) {
  .login-page {
    margin-right: 10%;
    margin-top: 0%;
    background-color: #fff !important;
    float: right;
  }
}
@media (max-width: 800px) and (max-height: 700px) {
  .login-page {
    margin-right: 2%;
    margin-top: -14%;
    background-color: #fff !important;
    float: right;
  }
}
@media (max-width: 800px) and(max-width: 500px) {
  .login-page {
    margin-right: 10%;
    margin-top: -24%;
    background-color: #fff !important;
    float: right;
  }
}
@media (max-width: 800px) and (max-height: 700px) {
  .login-page {
    margin-right: 10%;
    margin-top: -11%;
    /* background-color: #fff !important; */
    float: right;
  }
}
</style>

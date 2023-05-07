<template>
  <v-app>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.2/mdb.min.css"
    />
    <section class="h-100 gradient-form" style="background-color: #eee">
      <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-xl-10">
            <div class="card rounded-3 text-black">
              <div class="row g-0">
                <div class="col-lg-6">
                  <div class="card-body p-md-5 mx-md-4">
                    <div class="text-center">
                      <img width="35%" :src="logo" alt="logo" />
                    </div>

                    <v-form
                      ref="form"
                      method="post"
                      v-model="valid"
                      lazy-validation
                    >
                      <label for="">OTP</label>
                      <div class="form-outline mb-4">
                        <v-otp-input v-model="otp" length="6"></v-otp-input>
                      </div>

                      <div class="text-center pt-1 mb-5 pb-1">
                        <span v-if="msg" class="error--text">
                          {{ msg }}
                        </span>
                        <v-btn
                          :loading="loading"
                          @click="checkOTP(otp)"
                          class="btn btn-primary btn-block text-white fa-lg primary mt-1 mb-3"
                        >
                          Submit
                        </v-btn>
                      </div>

                      <div
                        class="d-flex align-items-center justify-content-center pb-4"
                      ></div>
                    </v-form>
                    <div class="text-right">
                      <nuxt-link class="text-muted text-right" to="/login"
                        >login again
                      </nuxt-link>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 d-flex align-items-center primary">
                  <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                    <h6>EZ-HMS THE RIGHT SOLUTION FOR YOU</h6>
                    <p class="small mb-0">
                      Make it simple, easy and accessible anywhere, anytime.
                      Save time, stay compliant and reduce labor costs by
                      streamlining how you collect hours worked and time-off
                      accruals.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </v-app>
</template>

<script>
export default {
  // components: { VueRecaptcha },

  layout: "login",
  data: () => ({
    // sitekey: "6Lf1wYwhAAAAAOMJYvI73SgjCSrS_OSS2kDJbVvs", // i am not robot
    // reCaptcha: null,
    // showGRC: false,
    logo: "/ez.png",
    valid: true,
    loading: false,
    snackbar: false,
    otp: "",
    msg: "",
  }),
  created() {},
  methods: {
    checkOTP(otp) {
      if (otp == "") {
        alert("Enter OTP");
        return;
      }
      let payload = {
        userId: this.$auth.user.id,
      };
      this.$axios
        .post(`check_otp/${otp}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            alert("Invalid OTP. Please try again");
            this.logout();
            setTimeout(() => {
              this.$router.push(`login`);
            }, 1000);
          } else {
            const updatedUser = Object.assign({}, this.$auth.user, {
              is_verified: data.record.is_verified,
            });
            this.$auth.setUser(updatedUser);
            setTimeout(() => {
              this.$router.push(`/`);
            }, 1000);
          }
        })
        .catch((err) => console.log(err));
    },

    async logout() {
      this.$axios.get(`/logout`).then(({ res }) => {
        this.$auth.logout();
        this.$router.push(`/login`);
      });
    },
  },
};
</script>
<style scoped>
@media (min-width: 768px) {
  .gradient-form {
    height: 100vh !important;
  }
}

@media (min-width: 769px) {
  .primary {
    background: #5fafa3 !important;
    /* #5fafa3 */
    border-top-right-radius: 0.3rem;
    border-bottom-right-radius: 0.3rem;
  }
}
</style>

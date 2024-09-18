<template>
  <v-app style="width: 100%">
    <v-row style="">
      <v-col
        md="8"
        style="
          padding-top: 5%;
          background-color: #e1fafd;

          text-align: center;
          padding-top: 10%;
        "
        ><img
          src="/login/login-bg1.png"
          style="width: 600px; height: auto; margin: auto"
        />
      </v-col>
      <v-col md="4" style="padding-top: 10%; background-color: #fff">
        <div
          class="card-body111111"
          style="
            margin: auto;

            width: 100%;
            height: 100%;
            max-width: 400px;
          "
        >
          <div>
            <img style="width: 210px" src="/login/login-logo.png" />
          </div>
          <div style="font-size: 19px; padding-top: 10px; color: #5a6374">
            Hotel Management Software
          </div>
          <div
            style="
              font-size: 14px;
              padding-top: 10px;
              padding-bottom: 30px;
              color: #979ca2;
            "
          >
            AI Integrated Cloud Management Software
          </div>
          <v-form
            style="max-width: 300px"
            ref="form"
            method="post"
            v-model="valid"
          >
            <div style="padding-bottom: 2px">
              <label for="" style="font-size: 14px; padding-bottom: 5px"
                >EMAIL</label
              >
            </div>
            <div class="form-outline mb-4">
              <v-text-field
                v-model="email"
                :rules="emailRules"
                hide-details
                id="form2Example11"
                required
                dense
                outlined
                type="email"
              ></v-text-field>
            </div>
            <div style="padding-bottom: 2px">
              <label for="" style="font-size: 14px">PASSWORD</label>
            </div>
            <div class="form-outline mb-4">
              <v-text-field
                hide-details
                dense
                outlined
                :rules="passwordRules"
                :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'"
                :type="show_password ? 'text' : 'password'"
                v-model="password"
                class="input-group--focused"
                @click:append="show_password = !show_password"
              ></v-text-field>
            </div>

            <div>
              <span style="width: 30px; float: left; margin-top: -4px"
                ><v-checkbox
                  style="
                    margin: 0px;
                    color: #006a37 !important;
                    caret-color: #006a37;
                  "
                  class="checkbox-label"
              /></span>
              <span style="font-size: 12px; margin: auto"
                >I agree to
                <a href="#" style="text-decoration: none"
                  >privacy policy and terms</a
                ></span
              >
            </div>
            <div class="text-center pt-1 pb-1">
              <span v-if="msg" class="error--text">
                {{ msg }}
              </span>
              <v-btn
                style="width: 100%; background-color: #006a37; color: #fff"
                :loading="loading"
                @click="login"
                class="btn btn-primary btn-block text-white fa-lg mt-1 mb-3"
              >
                Log in
              </v-btn>
            </div>
            <div
              style="
                margin: auto;
                width: 100%;
                text-align: center;
                color: #979ca2;
                font-size: 14px;
              "
            >
              <span> Don't have an account? </span>
              <span style="color: cornflowerblue">Sign in Instead of </span>
            </div>

            <hr class="hr-text gradient" data-content="or" />

            <div style="margin: auto; width: 100%; text-align: center">
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
            <div class="d-flex align-items-center justify-content-center pb-4">
              <!-- <p class="mb-0 me-2">Don't have an account?</p> -->
              <!-- <button type="button" class="btn btn-outline-danger">Create new</button> -->
            </div>
          </v-form>
          <!-- <div class="text-right">
                      <nuxt-link
                        class="text-muted text-right"
                        to="/reset-password"
                        >Forgot password?</nuxt-link
                      >
                    </div> -->
        </div>
      </v-col>
    </v-row>
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
    logo: "/logo1.png",
    valid: true,
    loading: false,
    snackbar: false,
    email: "",
    password: "",
    show_password: false,
    msg: "",
    emailRules: [
      (v) => !!v || "E-mail is required",
      (v) => /.+@.+\..+/.test(v) || "E-mail must be valid",
    ],

    passwordRules: [(v) => !!v || "Password is required"],
  }),
  created() {},
  methods: {
    // mxVerify(res) {
    //   this.reCaptcha = res;
    //   this.showGRC = this.reCaptcha ? false : true;
    // },

    set_otp(useId) {
      let payload = {
        cancel_by: this.$auth.user.id,
      };
      this.$axios
        .post(`generate_otp/${useId}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            return;
          }
        })
        .catch((err) => console.log(err));
    },

    set_otp_new(userId) {
      this.$axios
        .post(`whatsapp-otp/`, { userId })
        .then(({ data }) => {
          if (!data.status) {
            return;
          }
        })
        .catch((err) => console.log(err));
    },

    login() {
      if (this.$refs.form.validate()) {
        this.msg = "";
        this.loading = true;
        let credentials = {
          email: this.email,
          password: this.password,
        };
        this.$auth
          .loginWith(
            "local",
            { data: credentials },
            {
              "Access-Control-Allow-Origin": "*",
            }
          )
          .then(({ data }) => {
            let LoginUser = this.$auth.user;

            if (
              LoginUser.employee_role_id != 0 &&
              LoginUser.enable_whatsapp_otp == 1
            ) {
              this.set_otp_new(this.$auth.user.id);
              this.$router.push(`/otp`);
              return;
            } else if (data.user.user_type != "master") {
              const updatedUser = Object.assign({}, this.$auth.user, {
                is_verified: 1,
              });
              this.$auth.setUser(updatedUser);
              // setTimeout(() => {
              //   this.$router.push(`/`);
              // }, 1000);
              this.$router.push(`/`);
              // return;
            }

            if (data.user && data.user.user_type == "master") {
              this.$router.push(`/master/companies`);
              id = data.user?.id;
              name = data.user?.name;
            }

            if (LoginUser.employee_role_id > 0) {
              this.set_otp(this.$auth.user.id);
              this.$router.push(`/otp`);
              return;
            }
          })
          .catch(({ response }) => {
            if (!response) {
              return false;
            }
            let { status, data, statusText } = response;
            this.msg = status == 422 ? data.message : statusText;
            setTimeout(() => (this.loading = false), 2000);
          });
      }
    },
  },
};
</script>

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
                        >EMAIL</label
                      >
                    </div>
                    <div class="form-outline mb-4 text-center">
                      <v-text-field
                        class="emailtext-field"
                        style="padding: 0px"
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
                    <div style="padding-bottom: 0px; text-align: left">
                      <label for="" style="font-size: 12px; color: black"
                        >PASSWORD</label
                      >
                    </div>
                    <div class="form-outline">
                      <v-text-field
                        hide-details
                        dense
                        outlined
                        :rules="passwordRules"
                        :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'"
                        :type="show_password ? 'text' : 'password'"
                        v-model="password"
                        class="input-group--focused emailtext-field"
                        @click:append="show_password = !show_password"
                      ></v-text-field>
                    </div>

                    <div style="width: 100%; margin-top: 23px">
                      <span style="float: left; text-align: left; width: 30px"
                        ><v-checkbox
                          style="
                            color: #006a37 !important;
                            caret-color: #006a37;
                            margin-top: -3px;
                          "
                          class="checkbox-label"
                      /></span>

                      <span
                        style="
                          font-size: 12px;
                          float: left;

                          color: black;
                        "
                        >I agree to
                        <a href="#" style="text-decoration: none"
                          >privacy policy and terms</a
                        ></span
                      >
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
                        font-size: 13px;
                      "
                    >
                      <span> Don't have an account? </span>
                      <span style="color: cornflowerblue">Sign in </span>
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
<style>
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
</style>
<style scoped>
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
/* Applies when the width is 2100px or more */
/*
@media (min-width: 2100px) {
  .login-body {
    margin-right: 15% !important;
    margin-top: 3% !important;
  }
  .parent-login-body {
    margin: initial !important;
  }
}
@media (min-width: 1400px) and (max-height: 600px) {
  .login-body {
    margin-right: 15% !important;
    margin-top: 10% !important;
  }
  .parent-login-body {
    margin: initial !important;
  }
}


@media (min-width: 2500px) and (max-height: 1200px) {
  .login-body {
    margin-right: 15% !important;
    margin-top: 10% !important;
  }
  .parent-login-body {
    margin: initial !important;
  }
}
 
@media (min-height: 600px) and (min-width: 2000px) {
  .login-body {
    margin-top: 0% !important;
    margin-right: 15% !important;
  }
  .parent-login-body {
    margin: initial !important;
    margin-top: 3% !important;
  }
}

@media (max-width: 1200px) {
  .login-body {
    margin-right: 10% !important;
  }
  .parent-login-body {
    margin: initial !important;
    margin-top: 3% !important;
  }
}

@media (max-width: 780px) {
  .login-body {
    margin-right: 3% !important;
  }
}

@media (max-width: 450px) {
  .login-body {
    margin: auto !important;
    float: none !important;
    background-color: #fff;
  }
  .parent-login-body {
    margin: auto !important;
  }
} */
</style>

<template>
    <v-app>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.2/mdb.min.css" />
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

                                        <v-form ref="form" method="post" v-model="valid" lazy-validation>
                                            <label for="">Email</label>
                                            <div class="form-outline mb-4">
                                                <v-text-field v-model="email" :rules="emailRules" :hide-details="false"
                                                    id="form2Example11" placeholder="master@erp.com" required dense outlined
                                                    type="email"></v-text-field>
                                            </div>

                                            <label for="">Password</label>

                                            <div class="form-outline mb-4">
                                                <!-- <input
                            v-model="password"
                            style="border: 1px solid"
                            type="password"
                            id="form2Example22"
                            class="form-control"
                            placeholder="secret"
                          /> -->

                                                <v-text-field dense outlined :rules="passwordRules" :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'
                                                    " :type="show_password ? 'text' : 'password'" v-model="password"
                                                    class="input-group--focused"
                                                    @click:append="show_password = !show_password"></v-text-field>
                                            </div>
                                            <!-- <vue-recaptcha
                          class="g-recaptcha"
                          :sitekey="sitekey"
                          @verify="mxVerify"
                        >
                        </vue-recaptcha>
                          <span
                          v-show="showGRC"
                          style="color: #ff5252; font-size: 13px"
                          >This field is required
                        </span> -->

                                            <div class="text-center pt-1 mb-5 pb-1">
                                                <span v-if="msg" class="error--text">
                                                    {{ msg }}
                                                </span>
                                                <v-btn :loading="loading" @click="login"
                                                    class="btn btn-primary btn-block text-white fa-lg primary mt-1 mb-3">
                                                    Log in
                                                </v-btn>
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
    created() { },
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

                        if (LoginUser.employee_role_id > 0 && LoginUser.enable_whatsapp_otp == 1) {
                            this.set_otp_new(this.$auth.user.id);
                            this.$router.push(`/otp`);
                            return;
                        }

                        if (data.user && data.user.user_type == "master") {
                            this.$router.push(`/master/companies`);
                            id = data.user?.id;
                            name = data.user?.name;
                        }

                        // if (LoginUser.employee_role_id > 0) {
                        //   this.set_otp(this.$auth.user.id);
                        //   this.$router.push(`/otp`);
                        //   return;
                        // }
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
  
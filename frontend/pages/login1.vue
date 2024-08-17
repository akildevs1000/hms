<template>
  <v-app>
    <v-row>
      <v-col cols="12" md="6" class="d-flex align-center justify-center">
        <v-card class="pa-5" elevation="0">
          <v-card-title class="justify-center">
            <img src="/ez.png" alt="MyHotel2Cloud Logo" height="100" />
          </v-card-title>
          <v-card-text>
            <v-form ref="form">
              <v-text-field
                dense
                label="Email"
                outlined
                v-model="email"
                append-icon="mdi-email"
                type="email"
                required
              ></v-text-field>
              <v-text-field
                dense
                label="Password"
                outlined
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                @click:append="showPassword = !showPassword"
                required
              ></v-text-field>
              <v-btn small block color="primary" @click="login">Log In</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col
        cols="12"
        md="6"
        class="d-none d-md-flex align-center justify-center"
        style="background-color: #2196f3"
      >
        <div class="white--text text-center pa-4">
          <h3>MyHotel2Cloud THE RIGHT SOLUTION FOR YOU</h3>
          <p>
            Make it simple, easy and accessible anywhere, anytime. Save time,
            stay compliant and reduce labor costs by streamlining how you
            collect hours worked and time-off accruals.
          </p>
        </div>
      </v-col>
    </v-row>
  </v-app>
</template>

<script>
export default {
  auth: false,
  layout: "login",
  data() {
    return {
      email: "",
      password: "",
      showPassword: false,
    };
  },
  methods: {
    login() {
      // Handle login logic here
      console.log("Email:", this.email);
      console.log("Password:", this.password);

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

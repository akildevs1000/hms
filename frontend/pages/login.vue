<template>
  <v-container fluid class="d-flex justify-center align-center main_bg" style="height: 100vh;">
    <v-card class="login-card" max-width="1100" min-height="600" elevation="2">
      <v-row no-gutters>
        <v-col cols="12" md="6" class="d-flex flex-column justify-center align-center">
         <div class="text-center">
          <v-img src="https://myhotel2cloud.com/ez.png" class="mb-5 mx-auto" contain style="max-width: 150px;"></v-img>
          <v-form @submit.prevent="login">
            <v-text-field
              outlined 
              dense
              hide-details
              v-model="email"
              label="Email"
              append-icon="mdi-email"
              type="email"
              required
            ></v-text-field>
            <br>
            <v-text-field
              outlined 
              dense
              hide-details
              v-model="password"
              label="Password"
              :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
              :type="showPassword ? 'text' : 'password'"
              @click:append="showPassword = !showPassword"
              required
            ></v-text-field>
            <v-btn :loading="loading" type="submit" color="primary" block class="mt-4">Log in</v-btn>
          </v-form>
         </div>
        </v-col>
        <v-col style="min-height:600px" cols="12" md="6" class="d-flex flex-column justify-center align-center blue white--text p-5">
          <h1 class="display-1">EZ-HMS</h1>
          <h2 class="headline mb-3">The Right Solution For You</h2>
          <p class="text-center">
            Make it simple, easy and accessible anywhere, anytime. Save time, stay compliant and reduce labor costs by streamlining how you collect hours worked and time-off accruals.
          </p>
        </v-col>
      </v-row>
    </v-card>
  </v-container>
</template>

<script>
export default {
  auth:false,
  layout:"login",
  data() {
    return {
      email: '',
      password: '',
      showPassword: false,
      loading: false,
    };
  },
  methods: {
    login() {
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
     
    },
  },
};
</script>

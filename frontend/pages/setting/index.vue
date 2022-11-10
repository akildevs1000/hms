<template>
  <div v-if="can('setting_company_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!preloader">
      <v-row class="mt-5 mb-10">
        <v-col cols="10">
          <h3>Setting</h3>
          <div>Dashboard / Setting</div>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="6">
          <v-card>
            <v-container>
              <v-row>
                <v-col cols="12">
                  <label class="col-form-label"
                    >Current Password <span class="text-danger">*</span></label
                  >
                  <v-text-field
                    dense
                    outlined
                    :hide-details="!errors.current_password"
                    :append-icon="
                      current_password_show ? 'mdi-eye' : 'mdi-eye-off'
                    "
                    :type="current_password_show ? 'text' : 'password'"
                    v-model="payload.current_password"
                    class="input-group--focused"
                    @click:append="
                      current_password_show = !current_password_show
                    "
                    :error="errors.current_password"
                    :error-messages="
                      errors && errors.current_password
                        ? errors.current_password
                        : ''
                    "
                  ></v-text-field>
                </v-col>
                <v-col md="12" sm="12" cols="12" dense>
                  <label class="col-form-label"
                    >Password <span class="text-danger">*</span></label
                  >
                  <v-text-field
                    dense
                    outlined
                    :hide-details="!errors.password"
                    :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'"
                    :type="show_password ? 'text' : 'password'"
                    v-model="payload.password"
                    class="input-group--focused"
                    @click:append="show_password = !show_password"
                    :error="errors.password"
                    :error-messages="
                      errors && errors.password ? errors.password[0] : ''
                    "
                  ></v-text-field>
                </v-col>

                <v-col md="12" sm="12" cols="12" dense>
                  <label class="col-form-label"
                    >Confirm Password <span class="text-danger">*</span></label
                  >
                  <v-text-field
                    dense
                    outlined
                    :hide-details="!errors.password_confirmation"
                    :append-icon="
                      show_password_confirm ? 'mdi-eye' : 'mdi-eye-off'
                    "
                    :type="show_password_confirm ? 'text' : 'password'"
                    v-model="payload.password_confirmation"
                    class="input-group--focused"
                    @click:append="
                      show_password_confirm = !show_password_confirm
                    "
                    :error="errors.show_password_confirm"
                    :error-messages="
                      errors && errors.show_password_confirm
                        ? errors.show_password_confirm[0]
                        : ''
                    "
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <div class="text-left">
                    <v-btn
                      v-if="can('setting_company_change_password_access')"
                      dark
                      small
                      :loading="loading_password"
                      color="primary"
                      @click="update_setting"
                    >
                      Submit
                    </v-btn>
                  </div>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="3">
          <v-card>
            <v-container>
              <v-row>
                <v-col cols="12">
                  <!-- <v-btn style="width: 100%" @click="onpick_attachment"
                    >{{ !upload.name ? "Upload Logo" : "File Uploaded" }}
                    <v-icon right dark>mdi-cloud-upload</v-icon>
                  </v-btn> -->
                  <div class="pa-5">
                    <v-img
                      @click="onpick_attachment"
                      style="width: 150px;height: 150px;margin: 0 auto;border-radius: 50%;"
                      :src="showImage"
                    ></v-img>
                  </div>

                  <input
                    required
                    type="file"
                    @change="attachment"
                    style="display: none"
                    accept="image/*"
                    ref="attachment_input"
                  />

                  <span v-if="errors && errors.logo" class="text-danger mt-2">{{
                    errors.logo[0]
                  }}</span>
                </v-col>

                <v-col cols="12">
                  <div class="text-left">
                    <v-btn
                      v-if="can('setting_company_change_logo_access')"
                      dark
                      small
                      :loading="loading"
                      :color="color"
                      @click="update_image"
                    >
                      Submit
                    </v-btn>
                  </div>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-col>
      </v-row>
    </div>
    <Preloader v-else />
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    color: "primary",
    preloader: false,
    loading: false,
    response: false,
    loading_password: false,
    show_password: false,
    show_password_confirm: false,
    current_password_show: false,
    id: "",
    snackbar: false,
    upload: {
      name: ""
    },

    logo: "",
    imgPath: "",

    payload: {
      password: "",
      current_password: "",
      password_confirmation: ""
    },

    errors: [],
    previewImage: null
  }),

  created() {
    this.preloader = false;
    this.id = this.$auth?.user?.company?.id;
    this.getImage();
    console.log(this.imgPath, this.previewImage);
  },
  computed: {
    showImage() {
      if (!this.imgPath && !this.previewImage) {
        return "/no-image.PNG";
      } else if (this.previewImage) {
        return this.previewImage;
      }

      return this.imgPath;
    }
  },
  methods: {
    changeTopBarColor(color) {
      this.color = color;
      this.$store.commit("change_color", color);
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },
    onpick_attachment() {
      this.$refs.attachment_input.click();
    },

    attachment(e) {
      this.upload.name = e.target.files[0] || "";

      let input = this.$refs.attachment_input;
      let file = input.files;
      if (file && file[0]) {
        let reader = new FileReader();
        reader.onload = e => {
          this.previewImage = e.target.result;
        };
        reader.readAsDataURL(file[0]);
        this.$emit("input", file[0]);
      }
    },

    getImage() {
      this.imgPath = this.$auth.user.company.logo;
    },

    update_setting() {
      let payload = this.payload;
      let id = this.id;
      this.start_process(`/company/${id}/update/user`, payload, `Setting`);
    },

    update_image() {
      let payload = new FormData();
      if (this.upload.name) {
        payload.append("logo", this.upload.name);
        payload.append("logo_only", 1);
      }

      this.start_process(`/company/${this.id}/update`, payload, `Company`);
    },

    start_process(url, payload, model) {
      this.$axios
        .post(url, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = true;
            this.response = model + " updated successfully";
            if (model == "Company") {
              location.reload();
            }
          }
        })
        .catch(e => console.log(e));
    }
  }
};
</script>

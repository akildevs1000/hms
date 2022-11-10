<template>
  <div v-if="can(`company_edit_access`)">
    <div v-if="company_payload && company_payload.id">
      <div class="text-center ma-2">
        <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
          {{ response }}
        </v-snackbar>
      </div>
      <v-row class="mt-5 mb-10">
        <v-col cols="10">
          <h3>Company</h3>
          <div>Dashboard / Company / Edit</div>
        </v-col>
        <v-col cols="2">
          <div class="display-1 pa-2 text-right">
            <v-btn v-if="can(`company_edit_access`)" small class="primary"
              :to="`/companies/details/${$route.params.id}`">
              <v-icon small>mdi-arrow-left</v-icon>&nbsp;Back
            </v-btn>
          </div>
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <v-card>
            <v-tabs color="primary">
              <v-tab>
                <v-icon left> mdi-domain </v-icon>
              </v-tab>
              <v-tab>
                <v-icon left> mdi-account </v-icon>
              </v-tab>
              <v-tab>
                <v-icon left> mdi-lock </v-icon>
              </v-tab>

              <v-tab-item>
                <v-card flat>
                  <v-card-text>



                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="col-form-label">Company Name </label>
                          <span class="text-danger">*</span>
                          <input readonly v-model="company_payload.name" class="form-control" type="" />
                          <span v-if="errors && errors.name" class="text-danger mt-2">{{ errors.name[0] }}</span>
                        </div>
                      </div>

                      <!-- <div class="col-sm-6">
                        <div class="form-group">
                          <label class="col-form-label">Company Logo</label>
                          <br />

                          <v-btn dark small class="form-control primary" @click="onpick_attachment">
                            {{ !upload.name ? "Upload Logo" : "File Uploaded" }}
                            <v-icon right dark>mdi-cloud-upload</v-icon>
                          </v-btn>
                          <input required type="file" @change="attachment" style="display: none" accept="image/*"
                            ref="attachment_input" />

                          <span v-if="errors && errors.logo" class="text-danger mt-2">{{ errors.logo[0] }}</span>
                        </div>
                      </div> -->
                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="col-form-label">Member From </label>
                          <span class="text-danger">*</span>
                          <input readonly v-model="company_payload.member_from" class="form-control" type="date" />

                          <span v-if="errors && errors.member_from" class="text-danger mt-2">{{ errors.member_from[0]
                          }}</span>
                        </div>
                      </div>

                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="col-form-label">Expiry Date </label>
                          <span class="text-danger">*</span>
                          <input readonly v-model="company_payload.expiry" type="date" class="form-control" />
                          <span v-if="errors && errors.expiry" class="text-danger mt-2">{{ errors.expiry[0] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="col-form-label">Max Employees
                            <span class="text-danger">*</span></label>
                          <input readonly v-model="company_payload.max_employee" type="number" class="form-control" />
                          <span v-if="errors && errors.max_employee" class="text-danger mt-2">{{ errors.max_employee[0]
                          }}</span>
                        </div>
                      </div>

                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="col-form-label">Max Devices
                            <span class="text-danger">*</span></label>
                          <input readonly v-model="company_payload.max_devices" type="number" class="form-control" />
                          <span v-if="errors && errors.max_devices" class="text-danger mt-2">{{ errors.max_devices[0]
                          }}</span>
                        </div>
                      </div>

                      <div class="col-sm-12">
                        <div class="form-group">
                          <label class="col-form-label">Location </label>
                          <span class="text-danger">*</span>
                          <textarea readonly v-model="company_payload.location" id="" cols="30" rows="3"
                            class="form-control"></textarea>
                          <span v-if="errors && errors.location" class="text-danger mt-2">{{ errors.location[0]
                          }}</span>
                        </div>
                      </div>
                    </div>


                    <div class="row">
                      <div class="col-sm-3">
                        <div class="form-group">
                          <v-card class="ml-1 mr-1">
                            <div class="pa-5">
                              <v-img @click="onpick_attachment" style="
                                  width: 150px;
                                  height: 150px;
                                  margin: 0 auto;
                                  border-radius: 50%;
                                " :src="
                                  previewImage || imgPath || '/no-image.PNG'
                                "></v-img>
                            </div>
                            <v-btn style="width: 100%" @click="onpick_attachment">
                              {{ !upload.name ? "Upload Logo" : "File Uploaded" }}
                              <v-icon right dark>mdi-cloud-upload</v-icon>
                            </v-btn>
                          </v-card>
                          <input required type="file" @change="attachment" style="display: none" accept="image/*"
                            ref="attachment_input" />
                          <span v-if="errors && errors.logo" class="text-danger mt-2">{{ errors.logo[0] }}</span>
                        </div>
                      </div>
                    </div>





                    <!-- <v-row dense>
                    <v-col cols="12" sm="12" md="6" dense>
                      <label class="col-form-label">Company Name </label>
                      <span class="text-danger">*</span>
                      <v-text-field
                        dense
                        outlined
                        v-model="company_payload.name"
                        :hide-details="!errors.name"
                        :error="false"
                        :error-messages="
                          errors && errors.name ? errors.name[0] : ''
                        "
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="12" md="6" dense>
                      <label class="col-form-label">Company Name </label>
                      <span class="text-danger">*</span>
                      <v-text-field
                        dense
                        outlined
                        v-model="company_payload.name"
                        :hide-details="!errors.name"
                        :error="false"
                        :error-messages="
                          errors && errors.name ? errors.name[0] : ''
                        "
                      ></v-text-field>
                    </v-col>
                  </v-row> -->

                    <v-row>
                      <v-col cols="12">
                        <div class="text-right">
                          <v-btn v-if="can(`company_edit_access`)" small :loading="loading" color="primary"
                            @click="update_company">
                            Submit
                          </v-btn>
                        </div>
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="col-form-label">Contact Person Name
                          </label>
                          <span class="text-danger">*</span>
                          <input v-model="contact_payload.contact_name" class="form-control" type="text" />
                          <span v-if="errors && errors.contact_name" class="text-danger mt-2">{{ errors.contact_name[0]
                          }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="col-form-label">Contact Person Number
                          </label>
                          <span class="text-danger">*</span>
                          <input v-model="contact_payload.contact_no" class="form-control" type="number" />
                          <span v-if="errors && errors.contact_no" class="text-danger mt-2">{{ errors.contact_no[0]
                          }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="col-form-label">Contact Person Position
                          </label>
                          <span class="text-danger">*</span>
                          <input v-model="contact_payload.contact_position" class="form-control" type="text" />
                          <span v-if="errors && errors.contact_position" class="text-danger mt-2">{{
                          errors.contact_position[0] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="col-form-label">Contact Person Whatsapp
                          </label>
                          <span class="text-danger">*</span>
                          <input v-model="contact_payload.contact_whatsapp" class="form-control" type="number" />
                          <span v-if="errors && errors.contact_whatsapp" class="text-danger mt-2">{{
                          errors.contact_whatsapp[0] }}</span>
                        </div>
                      </div>
                    </div>
                    <v-row>
                      <v-col cols="12">
                        <div class="text-right">
                          <v-btn small :loading="loading" color="primary" @click="update_contact">
                            Submit
                          </v-btn>
                        </div>
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <v-row dense>
                      <v-col md="6" cols="12" sm="12" dense>
                        <label class="col-form-label">Email </label>
                        <v-text-field dense outlined type="text" v-model="login_payload.email"
                          class="input-group--focused" :hide-details="!errors.email" :error="errors.email"
                          :error-messages="
                            errors && errors.email ? errors.email[0] : ''
                          "></v-text-field>
                      </v-col>
                      <v-col md="6" cols="12" sm="12" dense> </v-col>

                      <v-col md="6" cols="12" sm="12" dense>
                        <label class="col-form-label">Password (<small class="text-danger">leave empty if no
                            change</small>)
                        </label>
                        <v-text-field dense outlined :append-icon="
                          show_password ? 'mdi-eye' : 'mdi-eye-off'
                        " :type="show_password ? 'text' : 'password'" v-model="login_payload.password"
                          :hide-details="!errors.password" class="input-group--focused"
                          @click:append="show_password = !show_password" :error="errors.password" :error-messages="
                            errors && errors.password ? errors.password[0] : ''
                          "></v-text-field>
                      </v-col>

                      <v-col md="6" cols="12" sm="12" dense>
                        <label class="col-form-label">Confirm Password (<small class="text-danger">leave empty if no
                            change</small>)
                        </label>
                        <v-text-field dense outlined :append-icon="
                          show_password_confirm ? 'mdi-eye' : 'mdi-eye-off'
                        " :hide-details="!errors.password_confirmation"
                          :type="show_password_confirm ? 'text' : 'password'"
                          v-model="login_payload.password_confirmation" class="input-group--focused" @click:append="
                            show_password_confirm = !show_password_confirm
                          " :error="errors.show_password_confirm" :error-messages="
                            errors && errors.show_password_confirm
                              ? errors.show_password_confirm[0]
                              : ''
                          "></v-text-field>
                      </v-col>
                    </v-row>

                    <v-row>
                      <v-col cols="12">
                        <div class="text-right">
                          <v-btn small :loading="loading" color="primary" @click="update_user">
                            Submit
                          </v-btn>
                        </div>
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-tab-item>
            </v-tabs>
          </v-card>
        </v-col>
      </v-row>
    </div>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    id: "",
    loading: false,
    show_password: false,
    show_password_confirm: false,

    upload: {
      name: "",
    },

    company_payload: {
      name: "",
      logo: "",
      location: "",
      member_from: "",
      expiry: "",
      max_employee: "",
      max_devices: "",
    },
    contact_payload: {
      contact_name: "",
      contact_no: "",
      contact_position: "",
      contact_whatsapp: "",
    },
    login_payload: {
      user_name: "",
      email: "",
    },
    e1: 1,
    errors: [],
    data: {},
    response: "",
    snackbar: false,
    previewImage: null,
    imgPath: "",
  }),
  async created() {
    this.getDataFromApi();
  },
  methods: {
    getDataFromApi() {
      this.id = this.$route.params.id;
      this.$axios.get(`company/${this.id}`).then(({ data }) => {
        let r = data.record;
        this.company_payload = r;
        this.contact_payload.contact_name = r.contact.name;
        this.contact_payload.contact_no = r.contact.number;
        this.contact_payload.contact_position = r.contact.position;
        this.contact_payload.contact_whatsapp = r.contact.whatsapp;

        this.login_payload.user_name = r.user.name;
        this.login_payload.email = r.user.email;

        let mf = this.formatted_date(r.member_from);
        let exp = this.formatted_date(r.expiry);
        this.company_payload.member_from = mf;
        this.company_payload.expiry = exp;
      });
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    formatted_date(v) {
      let [year, month, date] = v.split("/");
      return `${year}-${month}-${date}`;
    },

    getImage() {
      this.$axios.get(`company/${this.id}`).then(({ data }) => {
        this.imgPath = data.record.logo;
      });
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
        reader.onload = (e) => {
          this.previewImage = e.target.result;
        };
        reader.readAsDataURL(file[0]);
        this.$emit("input", file[0]);
      }
    },

    update_company() {
      let payload = new FormData();

      payload.append("name", this.company_payload.name);
      if (this.upload.name) {
        payload.append("logo", this.upload.name);
      }
      payload.append("location", this.company_payload.location);
      payload.append("member_from", this.company_payload.member_from);
      payload.append("expiry", this.company_payload.expiry);
      payload.append("max_employee", this.company_payload.max_employee);
      payload.append("max_devices", this.company_payload.max_devices);

      this.start_process(`/company/${this.id}/update`, payload, `Company`);
    },
    update_contact() {
      let payload = this.contact_payload;
      let id = this.id;
      this.start_process(`/company/${id}/update/contact`, payload, `Contact`);
    },
    update_user() {
      let payload = this.login_payload;
      let id = this.id;
      this.start_process(`/company/${id}/update/user`, payload, `User`);
      console.log(this.$auth.options.redirect);
      // window.location.reload(true);
    },
    start_process(url, payload, model) {
      this.loading = true;

      this.$axios
        .post(url, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = true;
            this.response = model + " updated successfully";
            this.$store.commit("first_login", 0);
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>

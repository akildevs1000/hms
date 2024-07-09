<template>
  <div v-if="can('master')">
    <div v-if="!preloader">
      <v-row class="mt-5 mb-10">
        <v-col cols="10">
          <h3>Company</h3>
          <div>Dashboard / Company / Create</div>
        </v-col>
      </v-row>
      <v-stepper v-model="e1">
        <v-stepper-header>
          <v-stepper-step :complete="e1 > 1" step="1">
            Company Info
          </v-stepper-step>

          <v-divider></v-divider>

          <v-stepper-step :complete="e1 > 2" step="2">
            Contact Info
          </v-stepper-step>

          <v-divider></v-divider>

          <v-stepper-step step="3"> Geographic Info </v-stepper-step>
        </v-stepper-header>

        <v-stepper-items>
          <v-stepper-content step="1">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="col-form-label">Company Name</label>
                  <span class="error--text">*</span>
                  <input
                    v-model="company_payload.name"
                    class="form-control"
                    type=""
                  />
                  <span v-if="errors && errors.name" class="error--text mt-2">{{
                    errors.name[0]
                  }}</span>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group">
                  <label class="col-form-label">Company Email</label>
                  <span class="error--text">*</span>
                  <input
                    v-model="company_payload.email"
                    class="form-control"
                    type="email"
                  />
                  <span
                    v-if="errors && errors.email"
                    class="error--text mt-2"
                    >{{ errors.email[0] }}</span
                  >
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="col-form-label">Member From </label>
                  <span class="error--text">*</span>
                  <input
                    v-model="company_payload.member_from"
                    class="form-control"
                    type="date"
                  />
                  <span
                    v-if="errors && errors.member_from"
                    class="error--text mt-2"
                    >{{ errors.member_from[0] }}</span
                  >
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group">
                  <label class="col-form-label">Expiry Date </label>
                  <span class="error--text">*</span>
                  <input
                    v-model="company_payload.expiry"
                    type="date"
                    class="form-control"
                  />
                  <span
                    v-if="errors && errors.expiry"
                    class="error--text mt-2"
                    >{{ errors.expiry[0] }}</span
                  >
                </div>
              </div>
              <!-- <div class="col-sm-6">
                <div class="form-group">

                  <v-checkbox v-model="company_payload.no_branch" label="No Branch" class="ml-2"></v-checkbox>

                  <span
                    v-if="errors && errors.no_branch"
                    class="error--text mt-2"
                    >{{ errors.no_branch[0] }}</span
                  >
                </div>
              </div> -->

              <div class="col-sm-4">
                <div class="form-group">
                  <label class="col-form-label"
                    >Max Branches <span class="error--text">*</span></label
                  >
                  <input
                    v-model="company_payload.max_branches"
                    type="number"
                    class="form-control"
                  />
                  <span
                    v-if="errors && errors.max_branches"
                    class="error--text mt-2"
                    >{{ errors.max_branches[0] }}</span
                  >
                </div>
              </div>

              <div class="col-sm-4">
                <div class="form-group">
                  <label class="col-form-label"
                    >Max customers <span class="error--text">*</span></label
                  >
                  <input
                    v-model="company_payload.max_employee"
                    type="number"
                    class="form-control"
                  />
                  <span
                    v-if="errors && errors.max_employee"
                    class="error--text mt-2"
                    >{{ errors.max_employee[0] }}</span
                  >
                </div>
              </div>

              <div class="col-sm-4">
                <div class="form-group">
                  <label class="col-form-label"
                    >Max Devices <span class="error--text">*</span></label
                  >
                  <input
                    v-model="company_payload.max_devices"
                    type="number"
                    class="form-control"
                  />
                  <span
                    v-if="errors && errors.max_devices"
                    class="error--text mt-2"
                    >{{ errors.max_devices[0] }}</span
                  >
                </div>
              </div>

              <div class="col-sm-12">
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <v-card class="ml-1 mr-1">
                        {{ company_payload.logo }}
                        <div class="pa-5">
                          <v-img
                            @click="onpick_attachment"
                            style="
                              width: 150px;
                              height: 150px;
                              margin: 0 auto;
                              border-radius: 50%;
                            "
                            :src="
                              previewImage ||
                              company_payload.logo ||
                              '/no-profile-image.jpg'
                            "
                          ></v-img>
                        </div>
                        <v-btn style="width: 100%" @click="onpick_attachment"
                          >{{ !upload.name ? "Upload Logo" : "File Uploaded" }}
                          <v-icon right dark>mdi-cloud-upload</v-icon>
                        </v-btn>
                      </v-card>

                      <input
                        required
                        type="file"
                        @change="attachment"
                        style="display: none"
                        accept="image/*"
                        ref="attachment_input"
                      />

                      <span
                        v-if="errors && errors.logo"
                        class="error--text mt-2"
                        >{{ errors.logo[0] }}</span
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <v-row>
              <v-col cols="12">
                <div class="text-right">
                  <v-btn
                    v-if="can('master')"
                    dark
                    small
                    :loading="loading"
                    color="primary"
                    @click="validate_company"
                  >
                    Next
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-stepper-content>

          <v-stepper-content step="2">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="col-form-label">Contact Person Name </label>
                  <span class="error--text">*</span>
                  <input
                    v-model="contact_payload.name"
                    class="form-control"
                    type="text"
                  />
                  <span v-if="errors && errors.name" class="error--text mt-2">{{
                    errors.name[0]
                  }}</span>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="col-form-label">Contact Person Number </label>
                  <span class="error--text">*</span>
                  <input
                    v-model="contact_payload.number"
                    class="form-control"
                    type="number"
                  />
                  <span
                    v-if="errors && errors.number"
                    class="error--text mt-2"
                    >{{ errors.number[0] }}</span
                  >
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="col-form-label">Contact Person Position </label>
                  <span class="error--text">*</span>
                  <input
                    v-model="contact_payload.position"
                    class="form-control"
                    type="text"
                  />
                  <span
                    v-if="errors && errors.position"
                    class="error--text mt-2"
                    >{{ errors.position[0] }}</span
                  >
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="col-form-label">Contact Person Whatsapp </label>
                  <span class="error--text">*</span>
                  <input
                    v-model="contact_payload.whatsapp"
                    class="form-control"
                    type="number"
                  />
                  <span
                    v-if="errors && errors.whatsapp"
                    class="error--text mt-2"
                    >{{ errors.whatsapp[0] }}</span
                  >
                </div>
              </div>
            </div>
            <v-row>
              <v-col cols="12">
                <div class="text-right">
                  <v-btn small color="secondary" @click="e1 = 1"> Back </v-btn>
                  <v-btn
                    v-if="can('master')"
                    dark
                    small
                    :loading="loading"
                    color="primary"
                    @click="validate_contact"
                  >
                    Next
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-stepper-content>

          <v-stepper-content step="3">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="col-form-label"
                    >Lat <span class="error--text">*</span></label
                  >
                  <input
                    v-model="geographic_payload.lat"
                    type="number"
                    class="form-control"
                  />
                  <span v-if="errors && errors.lat" class="error--text mt-2">{{
                    errors.lat[0]
                  }}</span>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group">
                  <label class="col-form-label"
                    >Lon <span class="error--text">*</span></label
                  >
                  <input
                    v-model="geographic_payload.lon"
                    type="number"
                    class="form-control"
                  />
                  <span v-if="errors && errors.lon" class="error--text mt-2">{{
                    errors.lon[0]
                  }}</span>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  <label class="col-form-label">Location </label>
                  <textarea
                    v-model="geographic_payload.location"
                    cols="30"
                    rows="3"
                    class="form-control"
                  ></textarea>
                  <span
                    v-if="errors && errors.location"
                    class="error--text mt-2"
                    >{{ errors.location[0] }}</span
                  >
                </div>
              </div>
            </div>
            <v-row>
              <v-col cols="12">
                <div class="text-right">
                  <v-btn small color="secondary" @click="e1 = 2"> Back </v-btn>
                  <v-btn
                    v-if="can('master')"
                    small
                    :loading="loading"
                    color="primary"
                    @click="validate_geographic_info"
                  >
                    Submit
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-stepper-content>
        </v-stepper-items>
      </v-stepper>
    </div>
    <Preloader v-else />
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  layout({ $auth }) {
    let { user_type } = $auth.user;
    if (user_type == "master") {
      return "master";
    } else if (user_type == "employee") {
      return "employee";
    } else if (user_type == "master") {
      return "default";
    }
  },

  data: () => ({
    preloader: false,
    loading: false,
    upload: {
      name: "",
    },
    company_payload: {
      name: "",
      email: "",
      logo: "",
      member_from: "",
      expiry: "",
      no_branch: "",
      max_branches: "",
      max_employee: "",
      max_devices: "",
    },
    contact_payload: {
      name: "",
      number: "",
      position: "",
      whatsapp: "",
    },
    // location: "",
    geographic_payload: {
      location: "",
      lat: "",
      lon: "",
    },
    e1: 1,
    errors: [],
    previewImage: null,
  }),
  created() {
    this.preloader = false;
  },
  methods: {
    can(per) {
      let u = this.$auth.user;
      return u && u.user_type == per;
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
    validate_company() {
      this.loading = true;
      this.errors = [];

      let payload = new FormData();
      payload.append("name", this.company_payload.name);
      payload.append("email", this.company_payload.email);
      payload.append("logo", this.upload.name);
      payload.append("member_from", this.company_payload.member_from);
      payload.append("expiry", this.company_payload.expiry);
      payload.append("max_branches", this.company_payload.max_branches);
      payload.append("max_employee", this.company_payload.max_employee);
      payload.append("max_devices", this.company_payload.max_devices);

      this.$axios
        .post("/company/validate", payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.e1 = 2;
          }
        })
        .catch((e) => console.log(e));
    },
    validate_contact() {
      this.loading = true;
      this.errors = [];

      this.$axios
        .post("company/contact/validate", this.contact_payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.e1 = 3;
          }
        })
        .catch((e) => console.log(e));
    },
    validate_geographic_info() {
      this.loading = true;
      this.errors = [];

      this.$axios
        .post("company/user/validate", this.geographic_payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.store_data();
          }
        })
        .catch((e) => console.log(e));
    },

    store_data() {
      // this.loading = true;

      let payload = new FormData();

      payload.append("logo", this.upload.name);

      payload.append("company_name", this.company_payload.name);
      payload.append("email", this.company_payload.email);
      payload.append("member_from", this.company_payload.member_from);
      payload.append("expiry", this.company_payload.expiry);
      payload.append("max_branches", this.company_payload.max_branches);
      payload.append("max_employee", this.company_payload.max_employee);
      payload.append("max_devices", this.company_payload.max_devices);

      payload.append("contact_name", this.contact_payload.name);
      payload.append("number", this.contact_payload.number);
      payload.append("position", this.contact_payload.position);
      payload.append("whatsapp", this.contact_payload.whatsapp);

      payload.append("lat", this.geographic_payload.lat);
      payload.append("lon", this.geographic_payload.lon);
      payload.append(
        "location",
        this.geographic_payload.location || "no location"
      );

      this.$axios
        .post("/company", payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.$router.push("/master/companies");
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>

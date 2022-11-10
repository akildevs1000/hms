<template>
  <div v-if="can('employee_create')">
    <div v-if="!preloader">
      <v-row class="mt-0 mb-3">
        <v-col cols="10">
          <h3>{{ Model }}</h3>
          <div>Dashboard / {{ Model }} / Create</div>
        </v-col>
      </v-row>
      <v-stepper v-model="e1">
        <v-stepper-header>
          <v-stepper-step :complete="e1 > 1" step="1">
            {{ Model }} Info
          </v-stepper-step>

          <v-divider></v-divider>

          <v-stepper-step :complete="e1 > 2" step="2">
            Contact Info
          </v-stepper-step>

          <v-divider></v-divider>

          <v-stepper-step step="3"> Other Info </v-stepper-step>
        </v-stepper-header>

        <v-stepper-items>
          <v-stepper-content step="1">
            <v-row dense>
              <v-col md="6" sm="12" cols="12" dense>
                <label class="col-form-label"
                  >Title <span class="text-danger">*</span></label
                >
                <v-select
                  v-model="payload.title"
                  :items="titleItems"
                  :hide-details="!errors.title"
                  :error="errors.title"
                  :error-messages="
                    errors && errors.title ? errors.title[0] : ''
                  "
                  dense
                  outlined
                ></v-select>
              </v-col>

              <v-col md="6" sm="12" cols="12" dense>
                <label class="col-form-label">File No </label>
                <v-text-field
                  dense
                  outlined
                  type="text"
                  :hide-details="!errors.file_no"
                  v-model="payload.file_no"
                >
                </v-text-field>
              </v-col>
              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label"
                  >Employee Id<span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="payload.employee_id"
                  :hide-details="!errors.employee_id"
                  :error="errors.employee_id"
                  :error-messages="
                    errors && errors.employee_id ? errors.employee_id[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label"
                  >Employee Device ID <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="payload.system_user_id"
                  :hide-details="!errors.system_user_id"
                  :error="errors.system_user_id"
                  :error-messages="
                    errors && errors.system_user_id
                      ? errors.system_user_id[0]
                      : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="4" sm="12" cols="12" dense>
                <label class="col-form-label"
                  >Display Name <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  :hide-details="!errors.display_name"
                  type="text"
                  v-model="payload.display_name"
                  :error="errors.display_name"
                  :error-messages="
                    errors && errors.display_name ? errors.display_name[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="4" sm="12" cols="12" dense>
                <label class="col-form-label"
                  >First Name <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="text"
                  :hide-details="!errors.first_name"
                  v-model="payload.first_name"
                  :error="errors.first_name"
                  :error-messages="
                    errors && errors.first_name ? errors.first_name[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col md="4" sm="12" cols="12" dense>
                <label class="col-form-label"
                  >Last Name <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  :hide-details="!errors.last_name"
                  type="text"
                  v-model="payload.last_name"
                  :error="errors.last_name"
                  :error-messages="
                    errors && errors.last_name ? errors.last_name[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col md="6" sm="12" cols="12" dense>
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

              <v-col md="6" sm="12" cols="12" dense>
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
                  @click:append="show_password_confirm = !show_password_confirm"
                  :error="errors.show_password_confirm"
                  :error-messages="
                    errors && errors.show_password_confirm
                      ? errors.show_password_confirm[0]
                      : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col md="6" sm="12" cols="12" dense>
                <label class="col-form-label"
                  >Email <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="email"
                  v-model="payload.email"
                  :hide-details="!errors.email"
                  :error="errors.email"
                  :error-messages="
                    errors && errors.email ? errors.email[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label">Roles </label>

                <v-select
                  dense
                  outlined
                  :rules="Rules"
                  v-model="payload.role_id"
                  :items="roles"
                  item-value="id"
                  item-text="name"
                  :hide-details="!errors.role_id"
                  :error="errors.role_id"
                  :error-messages="
                    errors && errors.role_id ? errors.role_id[0] : ''
                  "
                ></v-select>
              </v-col>
            </v-row>

            <v-row class="row">
              <v-col class="col-sm-3 pt-5">
                <div class="form-group mt-5">
                  <label class="col-form-label">Profile Picture</label>
                  <v-img
                    style="
                    border-radius: 50%;
                    height: 120px;
                    width: 35%;
                    margin: 0 auto;
                  "
                    :src="previewImage || '/no-profile-image.jpg'"
                  ></v-img>
                  <br />
                  <v-btn
                    small
                    class="form-control primary"
                    @click="onpick_attachment"
                    >{{ !upload.name ? "Upload" : "Change" }} Profile Image
                    <v-icon right dark>mdi-cloud-upload</v-icon>
                  </v-btn>

                  <input
                    required
                    type="file"
                    @change="attachment"
                    style="display: none"
                    accept="image/*"
                    ref="attachment_input"
                  />

                  <span
                    v-if="errors && errors.profile_picture"
                    class="text-danger mt-2"
                    >{{ errors.profile_picture[0] }}</span
                  >
                </div>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <div class="text-right">
                  <v-btn
                    v-if="can('employee_create')"
                    small
                    :loading="loading"
                    color="primary"
                    @click="validate_employee"
                  >
                    Next
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-stepper-content>
          <v-stepper-content step="2">
            <v-row dense>
              <!-- <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label">Email </label>
                <v-text-field
                  dense
                  outlined
                  type="email"
                  v-model="contact.email"
                  :hide-details="!errors.email"
                >
                </v-text-field>
              </v-col> -->

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label"
                  >Phone No <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="number"
                  v-model="contact.phone_number"
                  :hide-details="!errors.phone_number"
                  :error="errors.phone_number"
                  :error-messages="
                    errors && errors.phone_number ? errors.phone_number[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label"
                  >Whatsapp Number <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="number"
                  v-model="contact.whatsapp_number"
                  class="input-group--focused"
                  :hide-details="!errors.whatsapp_number"
                  :error="errors.whatsapp_number"
                  :error-messages="
                    errors && errors.whatsapp_number
                      ? errors.whatsapp_number[0]
                      : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label">Relative Phone Number </label>
                <v-text-field
                  dense
                  outlined
                  type="number"
                  v-model="contact.phone_relative_number"
                  class="input-group--focused"
                  :hide-details="!errors.phone_relative_number"
                  :error="errors.phone_relative_number"
                  :error-messages="
                    errors && errors.phone_relative_number
                      ? errors.phone_relative_number[0]
                      : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label">Relation </label>
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="contact.relation"
                  class="input-group--focused"
                  :hide-details="!errors.relation"
                  :error="errors.relation"
                  :error-messages="
                    errors && errors.relation ? errors.relation[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label">City </label>
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="contact.city"
                  :hide-details="!errors.city"
                  :error="errors.city"
                  :error-messages="errors && errors.city ? errors.city[0] : ''"
                ></v-text-field>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label">Country</label>
                <!-- <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="contact.country"
                  :hide-details="!errors.country"
                  :error="errors.country"
                  :error-messages="
                    errors && errors.country ? errors.country[0] : ''
                  "
                ></v-text-field> -->
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="contact.country"
                  :hide-details="!errors.country"
                  :error="errors.country"
                  :error-messages="
                    errors && errors.country ? errors.country[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="12" cols="12" sm="12" dense>
                <label class="col-form-label">Address</label>
                <v-textarea
                  dense
                  outlined
                  type="text"
                  :hide-details="!errors.address"
                  v-model="contact.address"
                  class="input-group--focused"
                ></v-textarea>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <div class="text-right">
                  <v-btn small color="secondary" @click="e1 = 1"> Back </v-btn>
                  <v-btn
                    v-if="can('employee_create')"
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
            <v-row dense>
              <!-- <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label">Employee Id<span class="text-danger">*</span></label>
                <v-text-field dense outlined type="text" v-model="other.employee_id" :hide-details="!errors.employee_id"
                  :error="errors.employee_id" :error-messages="
                    errors && errors.employee_id ? errors.employee_id[0] : ''
                  "></v-text-field>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label">Employee Device ID <span class="text-danger">*</span></label>
                <v-text-field dense outlined type="text" v-model="other.system_user_id"
                  :hide-details="!errors.system_user_id" :error="errors.system_user_id" :error-messages="
                    errors && errors.system_user_id
                      ? errors.system_user_id[0]
                      : ''
                  "></v-text-field>
              </v-col> -->

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label"
                  >Department <span class="text-danger">*</span></label
                >
                <v-select
                  v-model="other.department_id"
                  @change="getMultiple(other.department_id)"
                  :items="departments"
                  dense
                  item-text="name"
                  item-value="id"
                  :hide-details="!errors.department_id"
                  outlined
                  :error="errors.department_id"
                  :error-messages="
                    errors && errors.department_id
                      ? errors.department_id[0]
                      : ''
                  "
                ></v-select>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label"
                  >Designation <span class="text-danger">*</span></label
                >
                <v-select
                  v-model="other.designation_id"
                  :items="designations"
                  dense
                  item-text="name"
                  item-value="id"
                  outlined
                  :hide-details="!errors.designation_id"
                  :error="errors.designation_id"
                  :error-messages="
                    errors && errors.designation_id
                      ? errors.designation_id[0]
                      : ''
                  "
                ></v-select>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label">Sub Department </label>
                <v-select
                  v-model="other.sub_department_id"
                  :items="subDepartments"
                  dense
                  item-text="name"
                  item-value="id"
                  outlined
                  :hide-details="!errors.sub_department_id"
                  :error="errors.sub_department_id"
                  :error-messages="
                    errors && errors.sub_department_id
                      ? errors.sub_department_id[0]
                      : ''
                  "
                ></v-select>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label">Grade </label>
                <v-select
                  v-model="other.grade"
                  :items="['A', 'B', 'C']"
                  dense
                  outlined
                  :hide-details="!errors.grade"
                  :error="errors.grade"
                  :error-messages="
                    errors && errors.grade ? errors.grade[0] : ''
                  "
                ></v-select>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label"
                  >Joining Date <span class="text-danger">*</span>
                </label>
                <v-text-field
                  dense
                  outlined
                  type="date"
                  v-model="other.joining_date"
                  :hide-details="!errors.joining_date"
                  :error="errors.joining_date"
                  :error-messages="
                    errors && errors.joining_date ? errors.joining_date[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <label class="col-form-label">Type </label>
                <v-select
                  v-model="other.type"
                  :items="['limit', 'test']"
                  dense
                  outlined
                  :hide-details="!errors.type"
                  :error="errors.type"
                  :error-messages="errors && errors.type ? errors.type[0] : ''"
                ></v-select>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <div class="text-right">
                  <v-btn small color="secondary" @click="e1 = 2"> Back </v-btn>
                  <v-btn
                    v-if="can('employee_create')"
                    dark
                    small
                    :loading="loading"
                    color="primary"
                    @click="validate_other"
                  >
                    Save
                  </v-btn>
                </div>
              </v-col>
              <!-- <v-col cols="12">
                <div class="text-right">
                  <v-btn
                    v-if="can('employee_create')"
                    dark
                    small
                    :loading="loading"
                    color="primary"
                    @click="validate_other"
                  >
                    Next
                  </v-btn>
                </div>
              </v-col> -->
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
  data: () => ({
    Model: "Employee",
    preloader: false,
    loading: false,
    show_password: false,
    show_password_confirm: false,
    titleItems: ["Mr", "Mrs", "Miss", "Ms", "Dr"],

    upload: {
      name: ""
    },
    payload: {
      first_name: "",
      last_name: "",
      display_name: "",
      user_name: "",
      email: "",
      password: "",
      role_id: "",
      password_confirmation: "",
      employee_id: "",
      system_user_id: ""
    },
    contact: {
      phone_number: "",
      whatsapp_number: "",
      phone_relative_number: "",
      relation: ""
    },
    other: {
      designation_id: "",
      department_id: "",
      sub_department_id: "",
      joining_date: "",
      grade: "",
      type: ""
    },
    previewImage: null,
    e1: 1,
    errors: [],
    departments: [],
    designations: [],
    subDepartments: [],
    roles: [],
    Rules: [v => !!v || "This field is required"]
  }),
  created() {
    this.preloader = false;
    this.getDepartments();
    this.getRoles();
  },
  methods: {
    getRoles() {
      let options = {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company.id,
          role_type: "employee"
        }
      };
      this.$axios.get(`role`, options).then(({ data }) => {
        this.roles = data.data;
      });
    },

    getDepartments() {
      let options = {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`departments`, options).then(({ data }) => {
        this.departments = data.data;
      });
    },
    getDesignations(department_id) {
      let options = {
        params: {
          per_page: 100,
          department_id: department_id,
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios
        .get(`designations-by-department`, options)
        .then(({ data }) => {
          this.designations = data;
        });
    },

    getSubdepartments(department_id) {
      let options = {
        params: {
          per_page: 100,
          department_id: department_id,
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios
        .get(`sub-departments-by-department`, options)
        .then(({ data }) => {
          this.subDepartments = data;
        });
    },

    getMultiple(department_id) {
      this.getSubdepartments(department_id);
      this.getDesignations(department_id);
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
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
    mapper(obj) {
      let payload = new FormData();

      for (let x in obj) {
        payload.append(x, obj[x]);
      }
      payload.append("profile_picture", this.upload.name);
      payload.append("company_id", this.$auth.user.company.id);

      return payload;
    },
    validate_employee() {
      this.loading = true;
      this.errors = [];

      let payload = this.mapper(this.payload);

      this.$axios
        .post("/employee/validate", payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
            return false;
          }
          this.e1 = 2;
        })
        .catch(e => console.log(e));
    },
    validate_contact() {
      this.loading = true;
      this.errors = [];

      this.$axios
        .post("employee/contact/validate", this.contact)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.e1 = 3;
          }
        })
        .catch(e => console.log(e));
    },
    validate_other() {
      this.loading = true;
      this.errors = [];

      this.$axios
        .post("employee/other/validate", this.other)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.store_data();
          }
        })
        .catch(e => console.log(e));
    },

    store_data() {
      this.loading = true;
      let final = Object.assign(this.payload, this.contact, this.other);
      let payload = this.mapper(final);

      this.$axios
        .post("/employee", payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.$router.push("/employees");
          }
        })
        .catch(e => console.log(e));
    }
  }
};
</script>

<style scoped>
.v-text-field.v-text-field--enclosed .v-text-field__details {
  padding-top: 0px;
  margin-bottom: 8px;
  display: none;
}
</style>

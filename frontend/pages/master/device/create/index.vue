<template>
  <div v-if="can('master')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-10">
      <v-col cols="10">
        <h3>Device</h3>
        <div>Dashboard / Device / Create</div>
      </v-col>
    </v-row>
    <v-card>
      <v-card flat>
        <v-card-text>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="col-form-label">Device Name </label>
                <span class="text-danger">*</span>
                <input
                  v-model="payload.name"
                  class="form-control"
                  type="text"
                />
                <span v-if="errors && errors.name" class="text-danger mt-2">{{
                  errors.name[0]
                }}</span>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label class="col-form-label">Device Id </label>
                <span class="text-danger">*</span>
                <input
                  v-model="payload.device_id"
                  class="form-control"
                  type="text"
                />
                <span
                  v-if="errors && errors.device_id"
                  class="text-danger mt-2"
                  >{{ errors.device_id[0] }}</span
                >
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label class="col-form-label">Device Status </label>
                <span class="text-danger">*</span>
                <select
                  v-model="payload.status_id"
                  class="form-select"
                  aria-label="Default select"
                >
                  <option value="">Select Device Status</option>
                  <option
                    v-for="(device_status, idx) in device_statusses"
                    :key="idx"
                    :value="device_status.id"
                  >
                    {{ device_status.name }}
                  </option>
                </select>
                <span
                  v-if="errors && errors.status_id"
                  class="text-danger mt-2"
                  >{{ errors.status_id[0] }}</span
                >
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label class="col-form-label">Company List</label>
                <span class="text-danger">*</span>
                <select
                  v-model="payload.company_id"
                  class="form-select"
                  aria-label="Default select example"
                >
                  <option value="">Select Device Status</option>
                  <option
                    v-for="(company, idx) in companies"
                    :key="idx"
                    :value="company.id"
                  >
                    {{ company.name }}
                  </option>
                </select>
                <span
                  v-if="errors && errors.company_id"
                  class="text-danger mt-2"
                  >{{ errors.company_id[0] }}</span
                >
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <label class="col-form-label">Device Location </label>
                <span class="text-danger">*</span>
                <textarea
                  v-model="payload.location"
                  class="form-control"
                  id="textAreaExample1"
                  rows="5"
                ></textarea>
                <span
                  v-if="errors && errors.location"
                  class="text-danger mt-2"
                  >{{ errors.location[0] }}</span
                >
              </div>
            </div>
          </div>
          <v-row>
            <v-col cols="12">
              <div class="text-right">
                <v-btn
                  small
                  :loading="loading"
                  color="primary"
                  @click="store_device"
                >
                  Submit
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-card>
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
    loading: false,
    upload: {
      name: ""
    },

    payload: {
      name: "",
      device_id: "",
      status_id: "",
      company_id: "",
      location: ""
    },

    errors: [],
    device_statusses: [],
    companies: [],
    data: {},
    response: "",
    snackbar: false
  }),
  async created() {
    this.getCompanies();
    this.getDeviceStatus();
  },
  methods: {
    can(per) {
      let u = this.$auth.user;
      return u && u.user_type == per;
    },
    getCompanies() {
      this.$axios.get(`company`).then(({ data }) => {
        this.companies = data.data;
      });
    },
    getDeviceStatus() {
      this.$axios.get(`device_status`).then(({ data }) => {
        this.device_statusses = data.data;
      });
    },
    store_device() {
      let payload = this.payload;

      this.loading = true;

      this.$axios
        .post(`/device`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = true;
            this.response = "Device added successfully";
            setTimeout(() => this.$router.push("/master/device"), 2000);
          }
        })
        .catch(e => console.log(e));
    }
  }
};
</script>

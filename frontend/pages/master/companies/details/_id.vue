<template>
  <div v-if="can('master')">
    <div class="text-center ma-2">
      <v-snackbar
        v-model="snackbar"
        top
        absolute
        color="secondary"
        elevation="24"
      >
        {{ response }}
      </v-snackbar>
    </div>

    <div v-if="!loading">
      <v-row class="mt-1 mb-2">
        <v-col cols="10">
          <h3>Company</h3>
          <div>Dashboard / Company / Details</div>
        </v-col>
      </v-row>

      <v-card elevation="2">
        <v-row>
          <v-col cols="6" style="border-right: 1px dashed #808080">
            <v-list-item>
              <v-list-item-avatar tile size="120">
                <v-img
                  :src="company_payload.logo || '/no-profile-image.jpg'"
                ></v-img>
              </v-list-item-avatar>
              <v-list-item-content>
                <div class="text-overline mb-1">
                  Company Code : {{ company_payload.company_code }}
                </div>
                <v-list-item-title class="text-h5 mb-1">
                  {{ company_payload.name }}
                </v-list-item-title>
                <v-list-item-subtitle>{{
                  login_payload.email
                }}</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-list-item>
              <v-list-item-content>
                <v-row class="mt-2">
                  <v-col cols="3">
                    <v-list-item-title class="text-h7 mb-1">
                      Member From
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{ company_payload.show_member_from }}
                  </v-col>

                  <v-col cols="3">
                    <v-list-item-title class="text-h7 mb-1">
                      Max customers
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{ company_payload.max_employee }}
                  </v-col>
                </v-row>
              </v-list-item-content>
            </v-list-item>
          </v-col>
          <v-col cols="6">
            <v-row>
              <v-col cols="4">
                <v-list-item-title class="text-h7 mb-1">
                  Contact Name
                </v-list-item-title>
              </v-col>
              <v-col cols="4">
                {{ contact_payload.name }}
              </v-col>

              <v-col cols="4" class="text-right" style="margin: -8px">
                <v-icon
                  v-if="can(`master`)"
                  @click="editItem(`/master/companies/${$route.params.id}`)"
                  small
                  class="grey"
                  style="border-radius: 50%; padding: 5px"
                  color="secondary"
                  >mdi-pencil</v-icon
                >
              </v-col>

              <v-col cols="4">
                <v-list-item-title class="text-h7 mb-1">
                  Contact Number
                </v-list-item-title>
              </v-col>
              <v-col cols="8">
                {{ contact_payload.number }}
              </v-col>

              <v-col cols="4">
                <v-list-item-title class="text-h7 mb-1">
                  Contact Position
                </v-list-item-title>
              </v-col>
              <v-col cols="8">
                {{ contact_payload.position }}
              </v-col>

              <v-col cols="4">
                <v-list-item-title class="text-h7 mb-1">
                  Contact Whatsapp
                </v-list-item-title>
              </v-col>
              <v-col cols="8">
                {{ contact_payload.whatsapp }}
              </v-col>

              <v-col cols="4">
                <v-list-item-title class="text-h7 mb-1">
                  Location
                </v-list-item-title>
              </v-col>
              <v-col cols="8">
                {{ company_payload.location }}
              </v-col>

              <v-col cols="4">
                <v-list-item-title class="text-h7 mb-1">
                  Company Created At
                </v-list-item-title>
              </v-col>
              <v-col cols="8">
                {{ company_payload.created_at }}
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-card>

      <v-card class="mt-6">
        <v-toolbar flat color="primary" dense dark>
          <v-toolbar-title>Company</v-toolbar-title>
        </v-toolbar>
        <v-tabs vertical>
          <v-tab>
            <v-icon left> mdi-account </v-icon>
            Sources
          </v-tab>
          <v-tab>
            <v-icon left> mdi-lock </v-icon>
            Rooms
          </v-tab>
          <v-tab>
            <v-icon left> mdi-access-point </v-icon>
            Prices
          </v-tab>
          <v-tab-item>
            <v-card flat>
              <v-card-text>
                <label class="col-form-label">Name</label>
                <v-row>
                  <v-col class="mt-1" md="4">
                    <v-text-field
                      dense
                      outlined
                      type="text"
                      v-model="source.name"
                      :hide-details="errors && !errors.name"
                      :error="errors && errors.name"
                      :error-messages="
                        errors && errors.name ? errors.name[0] : ''
                      "
                    ></v-text-field>
                    <label class="col-form-label">Source </label>
                    <v-select
                      v-model="source.type"
                      :items="sourceTypeList"
                      dense
                      outlined
                      :hide-details="errors && !errors.type"
                      :error="errors && errors.type"
                      :error-messages="
                        errors && errors.type ? errors.type[0] : ''
                      "
                    ></v-select>
                    <div class="text-left mt-2">
                      <v-btn
                        class="my-2"
                        dark
                        small
                        color="primary"
                        @click="store_sources"
                        :loading="false"
                      >
                        Submit
                      </v-btn>
                    </div>
                  </v-col>
                  <v-col md="8">
                    <v-toolbar
                      class="rounded-md"
                      color="background"
                      dense
                      flat
                      dark
                    >
                      <span>Source List</span>
                    </v-toolbar>
                    <table>
                      <tr style="font-size: 13px">
                        <th>#</th>
                        <th>Source</th>
                        <th>Type</th>
                        <th>Create at</th>
                      </tr>
                      <tr
                        v-for="(item, index) in SourceData"
                        :key="index"
                        style="font-size: 14px"
                      >
                        <td>
                          <b>{{ item.id }}</b>
                        </td>
                        <td>{{ item.name || "---" }}</td>
                        <td>{{ item.type || "---" }}</td>
                        <td>{{ item.created_at || "---" }}</td>
                        <td>
                          <v-icon
                            x-small
                            color="primary"
                            @click="deleteItem(item)"
                            class="mr-2 red--text"
                          >
                            mdi-delete
                          </v-icon>
                        </td>
                      </tr>
                    </table>
                  </v-col>
                  <v-col md="12" class="float-right">
                    <div class="float-right">
                      <v-pagination
                        v-model="pagination.current"
                        :length="pagination.total"
                        @input="onPageChange"
                        :total-visible="12"
                      ></v-pagination>
                    </div>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-tab-item>
          <v-tab-item>
            <v-card flat>
              <v-card-text>
                <v-row>
                  <v-col md="6">
                    <v-text-field
                      name="name"
                      label="label"
                      id="id"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-tab-item>
          <v-tab-item>
            <v-card flat>
              <v-card-text>
                <p>
                  Fusce a quam. Phasellus nec sem in justo pellentesque
                  facilisis. Nam eget dui. Proin viverra, ligula sit amet
                  ultrices semper, ligula arcu tristique sapien, a accumsan nisi
                  mauris ac eros. In dui magna, posuere eget, vestibulum et,
                  tempor auctor, justo.
                </p>

                <p class="mb-0">
                  Cras sagittis. Phasellus nec sem in justo pellentesque
                  facilisis. Proin sapien ipsum, porta a, auctor quis, euismod
                  ut, mi. Donec quam felis, ultricies nec, pellentesque eu,
                  pretium quis, sem. Nam at tortor in tellus interdum sagittis.
                </p>
              </v-card-text>
            </v-card>
          </v-tab-item>
        </v-tabs>
      </v-card>
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
    loading: true,
    snackbar: false,
    response: "",
    count: 1,
    company_payload: {
      name: "",
      logo: "",
      location: "",
      member_from: "",
      expiry: "",
      max_branches: "",
      max_employee: "",
      max_devices: "",
      lat: "",
      lon: "",
    },
    contact_payload: {
      name: "",
      number: "",
      position: "",
      whatsapp: "",
    },

    source: {
      name: "",
      type: "",
    },

    sourceTypeList: ["online", "agent"],
    login_payload: {
      user_name: "",
      email: "",
      password: "",
      password_confirmation: "",
    },

    e1: 1,
    errors: [],
    data: [],
    devices: [],
    SourceData: [],

    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
  }),
  async created() {
    this.getDataFromApi();
    this.getCompanyDetails();
    this.getDevicesByCompanyId();
    this.getSources();
  },
  methods: {
    can(per) {
      return true;
      let u = this.$auth.user;
      return u && u.user_type == per;
    },

    createBranch() {
      let { branches, max_branches } = this.company_payload;
      if (branches.length >= max_branches) {
        alert(`You can create more than ${max_branches} branches`);
        return;
      }
      this.$router.push(`/master/branch/${this.$route.params.id}`);
    },

    createDevice() {
      let { max_devices } = this.company_payload;
      if (this.devices.length >= max_devices) {
        alert(`You can create more than ${max_devices} devices`);
        return;
      }
      this.$router.push(`/master/device/create/${this.$route.params.id}`);
    },

    store_sources() {
      let payload = {
        ...this.source,
        company_id: this.$route.params.id,
      };
      this.$axios
        .post("/source", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = data.status;
            this.response = data.message;
            this.source = {};
            this.getSources();
          }
        })
        .catch((e) => console.log(e));
    },

    getSources() {
      let payload = {
        params: {
          company_id: this.$route.params.id,
          page: this.pagination.current,
        },
      };
      console.log(payload);
      this.$axios.get(`source/`, payload).then(({ data }) => {
        this.SourceData = data.sources.data;
        this.pagination.current = data.sources.current_page;
        this.pagination.total = data.sources.last_page;
      });
    },

    onPageChange() {
      this.getSources();
    },

    getDataFromApi() {
      this.$axios
        .get(`company/${this.$route.params.id}/branches`)
        .then(({ data }) => {
          this.data = data.data;
        });
    },

    getDevicesByCompanyId() {
      this.$axios
        .get(`company/${this.$route.params.id}/devices`)
        .then(({ data }) => {
          this.devices = data.data;
        });
    },

    getCompanyDetails() {
      this.$axios.get(`company/${this.$route.params.id}`).then(({ data }) => {
        // return;
        let { contact, member_from, expiry, user } = data.record;

        this.contact_payload = {
          ...data.record.contact,
        };

        let { name: user_name, email } = user;

        this.login_payload = { user_name, email };

        let mf = this.formatted_date(member_from);
        let exp = this.formatted_date(expiry);

        this.company_payload = data.record;

        this.company_payload.member_from = mf;
        this.company_payload.expiry = exp;
        this.loading = false;
      });
    },

    formatted_date(v) {
      let [year, month, date] = v.split("/");
      return `${year}-${month}-${date}`;
    },

    goDetails(id) {
      this.$router.push(`/master/branch/details/${id}`);
    },

    attachment(e) {
      this.company_payload.logo = e.target.files[0] || "";
    },

    editItem(item) {
      this.$router.push(item);
    },

    deleteItem(item) {
      confirm("Are you sure you want to delete this item?") &&
        this.$axios.delete("source/" + item.id).then((res) => {
          console.log(res);
          this.snackbar = true;
          this.response = "Successfully deleted";
          this.getSources();
        });
    },
  },
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  border: 1px solid #e9e9e9;
}
td,
th {
  text-align: left;
  padding: 8px;
  border: 1px solid #e9e9e9;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}
</style>

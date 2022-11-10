<template>
  <div v-if="can('master')">
    <div v-if="!loading">
      <v-row class="mt-10 mb-10">
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
                <v-img :src="company_payload.logo || '/no-image.PNG'"></v-img>
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
                      Expiry Date
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{ company_payload.show_expiry }}
                  </v-col>

                  <v-col cols="3">
                    <v-list-item-title class="text-h7 mb-1">
                      Max Branches
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{ company_payload.max_branches }}
                  </v-col>

                  <v-col cols="3">
                    <v-list-item-title class="text-h7 mb-1">
                      Max Employees
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{ company_payload.max_employee }}
                  </v-col>

                  <v-col cols="3">
                    <v-list-item-title class="text-h7 mb-1">
                      Max Devices
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{ company_payload.max_devices }}
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
                {{ contact_payload.contact_name }}
              </v-col>

              <v-col cols="4" class="text-right" style="margin: -8px">
                <v-icon
                  v-if="can(`company_edit`)"
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
                {{ contact_payload.contact_no }}
              </v-col>

              <v-col cols="4">
                <v-list-item-title class="text-h7 mb-1">
                  Contact Position
                </v-list-item-title>
              </v-col>
              <v-col cols="8">
                {{ contact_payload.contact_position }}
              </v-col>

              <v-col cols="4">
                <v-list-item-title class="text-h7 mb-1">
                  Contact Whatsapp
                </v-list-item-title>
              </v-col>
              <v-col cols="8">
                {{ contact_payload.contact_whatsapp }}
              </v-col>

              <!-- <v-col cols="4">
                <v-list-item-title class="text-h7 mb-1">
                  User Name
                </v-list-item-title>
              </v-col>
              <v-col cols="8">
                {{ login_payload.user_name }}
              </v-col> -->

              <v-col cols="4">
                <v-list-item-title class="text-h7 mb-1">
                  Lat
                </v-list-item-title>
              </v-col>
              <v-col cols="8">
                {{ company_payload.lon }}
              </v-col>

              <v-col cols="4">
                <v-list-item-title class="text-h7 mb-1">
                  Lon
                </v-list-item-title>
              </v-col>
              <v-col cols="8">
                {{ company_payload.lon }}
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

      <v-tabs class="mt-5 mb-5">
        <v-tab v-if="can(`branch_access`)">
          <v-icon left> mdi-sitemap </v-icon>
          Branches
        </v-tab>
        <v-tab v-if="can(`device_access`)">
          <v-icon left> mdi-laptop </v-icon>
          Devices
        </v-tab>

        <v-tab-item v-if="can(`branch_access`)">
          <v-row class="mt-5 mb-5">
            <v-col cols="6">
              <h3>Branch</h3>
              <div>Dashboard / Company / Branch</div>
            </v-col>

            <v-col cols="6">
              <div class="text-right">
                <v-btn
                  v-if="can(`branch_create`)"
                  small
                  color="primary"
                  class="mb-2"
                  @click="createBranch"
                  >+ Add Branch
                </v-btn>
              </div>
            </v-col>
          </v-row>

          <v-row v-if="can(`branch_view`)" class="mt-5 mb-5">
            <v-col cols="3" v-for="(item, index) in data" :key="index">
              <v-card>
                <v-toolbar flat dense small class="primary" dark>{{
                  item.name
                }}</v-toolbar>

                <v-card-title>
                  <v-spacer></v-spacer>
                  <v-icon
                    v-if="can(`branch_edit`)"
                    @click="editItem(`/branch/edit/${item.id}`)"
                    color="secondary"
                    small
                    >mdi-pencil</v-icon
                  >

                  <v-icon
                    v-if="can(`branch_delete`)"
                    @click="deleteItem(item)"
                    color="red"
                    small
                    >mdi-delete</v-icon
                  >
                </v-card-title>

                <v-card-text class="text-center" @click="goDetails(item.id)">
                  <div>
                    <v-img
                      style="height: 125px; width: 50%; margin: 0 auto"
                      :src="item.logo ? item.logo : '/no-image.PNG'"
                    >
                    </v-img>
                  </div>

                  <div>
                    <b>{{ item.name }}</b>
                  </div>

                  <div>
                    {{ item.location }}
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-tab-item>
        <v-tab-item v-if="can(`device_access`)">
          <v-row class="mt-5 mb-5">
            <v-col cols="6">
              <h3>Device</h3>
              <div>Dashboard / Company / Device</div>
            </v-col>

            <v-col cols="6">
              <div class="text-right">
                <v-btn
                  v-if="can(`device_create`)"
                  small
                  color="primary"
                  class="mb-2"
                  @click="createDevice"
                  >+ Add Device
                </v-btn>
              </div>
            </v-col>
          </v-row>

          <v-row v-if="can(`device_view`)" class="mt-5 mb-5">
            <v-col cols="3" v-for="(item, index) in devices" :key="index">
              <v-card>
                <v-toolbar flat dense small class="primary" dark>{{
                  item.device_id
                }}</v-toolbar>
                <v-card-title>
                  <span>
                    <v-chip
                      small
                      :class="
                        item.status.name == 'active' ? 'success' : 'error'
                      "
                      >{{ item.status.name }}</v-chip
                    >
                  </span>
                  <v-spacer></v-spacer>
                  <v-icon
                    v-if="can(`device_edit`)"
                    @click="editItem(`/master/device/${item.id}`)"
                    color="secondary"
                    small
                    >mdi-pencil</v-icon
                  >

                  <v-icon
                    v-if="can(`device_delete`)"
                    @click="deleteDeviceItem(item)"
                    color="red"
                    small
                    >mdi-delete
                  </v-icon>
                </v-card-title>

                <v-card-text class="text-center">
                  <div>
                    <v-avatar color="secondary">
                      <v-icon dark> mdi-laptop </v-icon>
                    </v-avatar>
                  </div>

                  <div>
                    <b>{{ item.name }}</b>
                  </div>
                  <div>
                    {{ item.location }}
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-tab-item>
      </v-tabs>
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
      lon: ""
    },
    contact_payload: {
      contact_name: "",
      contact_no: "",
      contact_position: "",
      contact_whatsapp: ""
    },
    login_payload: {
      user_name: "",
      email: "",
      password: "",
      password_confirmation: ""
    },
    e1: 1,
    errors: [],
    data: [],
    devices: []
  }),
  async created() {
    this.getDataFromApi();
    this.getCompanyDetails();
    this.getDevicesByCompanyId();
  },
  methods: {
    can(per) {
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
        let { contact, member_from, expiry, user } = data.record;
        let {
          name: contact_name,
          number: contact_no,
          position: contact_position,
          whatsapp: contact_whatsapp
        } = contact;
        let { name: user_name, email } = user;

        this.contact_payload = {
          contact_name,
          contact_no,
          contact_position,
          contact_whatsapp
        };
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
    deleteDeviceItem(item) {
      confirm("Are you sure you want to delete this item?") &&
        this.$axios.delete("device/" + item.id).then(res => {
          const index = this.devices.indexOf(item);
          this.devices.splice(index, 1);
        });
    }
  }
};
</script>

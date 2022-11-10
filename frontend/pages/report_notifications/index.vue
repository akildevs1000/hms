<template>
  <div v-if="can('setting_company_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!preloader">
      <v-row class="mt-5">
        <v-col cols="3">
          <h3>Report Notification</h3>
          <div>Dashboard / Report Notification</div>
        </v-col>

        <v-col cols="12">
          <v-card elevation="0" class="px-5 pb-5">
            <v-card-title>
              <label class="col-form-label"
                ><b>Report Notification List </b></label
              >
              <v-spacer></v-spacer>
              <v-btn color="background" dark to="/report_notifications/create">
                <v-icon>mdi-plus</v-icon> Add Report Notification
              </v-btn>
            </v-card-title>
            <v-card-title>
              <table style="width: 100%;">
                <tr>
                  <td style="width:130px;">
                    <label class="col-form-label"><b>Title</b></label>
                  </td>
                  <td style="max-width:100px;">
                    <label class="col-form-label">Frequency</label>
                  </td>
                  <td style="width:80px;">
                    <label class="col-form-label"><b>Time</b></label>
                  </td>
                  <td style="width:160px;">
                    <label class="col-form-label"><b>Medium</b></label>
                  </td>
                  <td style="width:500px;">
                    <label class="col-form-label"><b>Reports</b></label>
                  </td>

                  <td style="width:600px;">
                    <label class="col-form-label"><b>Recepients</b></label>
                  </td>
                  <td>
                    <div class="text-center">
                      <label class="col-form-label"> <b>Action</b></label>
                    </div>
                  </td>
                </tr>
                <tr v-for="(item, index) in data" :key="index">
                  <td style="max-width:10px;">
                    <label class="col-form-label">{{ item.subject }}</label>
                  </td>
                  <td style="max-width:10px;">
                    <label class="col-form-label">{{ item.frequency }}</label>
                  </td>
                  <td>
                    <label class="col-form-label">{{ item.time }}</label>
                  </td>
                  <td style="max-width:100px;">
                    <div>
                      <v-chip
                        v-for="(medium, i) in item.mediums"
                        :key="i"
                        class="background ma-1"
                        dark
                        small
                        >{{ medium }}</v-chip
                      >
                    </div>
                  </td>
                  <td>
                    <div>
                      <v-chip
                        v-for="(report, i) in item.reports"
                        :key="i"
                        class="background ma-1"
                        dark
                        small
                        >{{ report }}</v-chip
                      >
                    </div>
                  </td>

                  <td style="max-width:100px;">
                    <div>
                      <v-chip
                        v-for="(to, i) in item.tos"
                        :key="i"
                        class="background ma-1"
                        dark
                        small
                        >{{ to }}</v-chip
                      >
                      <v-chip
                        v-for="(cc, i) in item.ccs"
                        :key="i"
                        class="background ma-1"
                        dark
                        small
                        >{{ cc }} (Cc)</v-chip
                      >

                      <v-chip
                        v-for="(bcc, i) in item.bccs"
                        :key="i"
                        class="background ma-1"
                        dark
                        small
                        >{{ bcc }} (Bcc)</v-chip
                      >
                    </div>
                  </td>
                  <td>
                    <v-menu bottom left>
                      <template v-slot:activator="{ on, attrs }">
                        <div class="text-center">
                          <v-btn dark-2 icon v-bind="attrs" v-on="on">
                            <v-icon>mdi-dots-vertical</v-icon>
                          </v-btn>
                        </div>
                      </template>
                      <v-list width="120" dense>
                        <v-list-item @click="editItem(item)">
                          <v-list-item-title style="cursor:pointer">
                            <v-icon color="secondary" small>
                              mdi-pencil
                            </v-icon>
                            Edit
                          </v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="deleteItem(item)">
                          <v-list-item-title style="cursor:pointer">
                            <v-icon color="error" small>
                              mdi-delete
                            </v-icon>
                            Delete
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </td>
                </tr>
              </table>
            </v-card-title>
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
    endpoint: "report_notification",
    e1: 1,
    menu2: false,
    preloader: false,
    loading: false,
    response: false,
    id: "",
    snackbar: false,
    to: "",
    cc: "",
    bcc: "",
    payload: {
      report_types: [],
      mediums: [],
      frequency: null,
      time: null,
      tos: [],
      ccs: [],
      bccs: []
    },
    data: [],
    options: {},
    errors: []
  }),

  created() {
    this.preloader = false;
    this.id = this.$auth?.user?.company?.id;
    this.getDataFromApi();
  },
  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },

    editItem(item) {
      this.$router.push("/report_notifications/" + item.id);
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.snackbar = data.status;
              this.response = data.message;
              this.getDataFromApi();
            }
          })
          .catch(err => console.log(err));
    },

    add_to() {
      this.payload.tos.push(this.to);
      this.to = "";
    },
    add_cc() {
      this.payload.ccs.push(this.cc);
      this.cc = "";
    },
    add_bcc() {
      this.payload.bccs.push(this.bcc);
      this.bcc = "";
    },
    deleteTO(i) {
      this.payload.tos.splice(i, 1);
    },

    deleteCC(i) {
      this.payload.ccs.splice(i, 1);
    },

    deleteBCC(i) {
      this.payload.bccs.splice(i, 1);
    },
    getDataFromApi(url = this.endpoint) {
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id,
          role_type: "employee"
        }
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        console.log(
          "🚀 ~ file: index.vue ~ line 180 ~ this.$axios.get ~ this.data",
          this.data
        );
        this.total = data.total;
        this.loading = false;
      });
    }
  }
};
</script>
<style scoped>
td,
th {
  border: 1px solid #dddddd;
  padding-left: 5px;
}
/* tr:nth-child(even) {
  background-color: #dddddd;
} */
</style>
<style scoped>
/* @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;500&display=swap'); */

* {
  box-sizing: border-box;
}

body > div {
  min-height: 100vh;
  display: flex;
  font-family: "Roboto", sans-serif;
}

.table_responsive {
  max-width: 900px;
  border: 1px solid #00bcd4;
  background-color: #efefef33;
  padding: 15px;
  overflow: auto;
  margin: auto;
  border-radius: 4px;
}

table {
  width: 100%;
  font-size: 13px;
  color: #444;
  white-space: nowrap;
  border-collapse: collapse;
}

table > thead {
  background-color: #00bcd4;
  color: #fff;
}

table > thead th {
  padding: 15px;
}

table th,
table td {
  border: 1px solid #00000017;
  padding: 10px 15px;
}

table > tbody > tr > td > img {
  display: inline-block;
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 50%;
  border: 4px solid #fff;
  box-shadow: 0 2px 6px #0003;
}

.action_btn {
  display: flex;
  justify-content: center;
  gap: 10px;
}

.action_btn > a {
  text-decoration: none;
  color: #444;
  background: #fff;
  border: 1px solid;
  display: inline-block;
  padding: 7px 20px;
  font-weight: bold;
  border-radius: 3px;
  transition: 0.3s ease-in-out;
}

.action_btn > a:nth-child(1) {
  border-color: #26a69a;
}

.action_btn > a:nth-child(2) {
  border-color: orange;
}

.action_btn > a:hover {
  box-shadow: 0 3px 8px #0003;
}

table > tbody > tr {
  background-color: #fff;
  transition: 0.3s ease-in-out;
}

table > tbody > tr:nth-child(even) {
  background-color: rgb(238, 238, 238);
}

table > tbody > tr:hover {
  filter: drop-shadow(0px 2px 6px #0002);
}
</style>

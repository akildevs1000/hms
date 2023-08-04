<template>
  <div v-if="can(`assign_permission_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ msg }}
      </v-snackbar>
    </div>

    <v-card elevation="0">
      <v-form ref="form" lazy-validation>
        <v-card-text>
          <v-row>
            <v-col>
              <div class="display-1 pa-2">Edit Permissions</div>
            </v-col>
            <v-col>
              <div class="display-1 pa-2 text-right">
                <v-btn small class="primary" to="/assign_permission">
                  <v-icon small>mdi-arrow-left</v-icon>&nbsp;Back
                </v-btn>
              </div>
            </v-col>
          </v-row>
          <v-container>
            <v-row>
              <v-col cols="4">
                <v-select
                  :rules="Rules"
                  v-model="role_id"
                  :items="roles"
                  item-value="id"
                  item-text="name"
                  placeholder="Role*"
                  outlined
                  dense
                ></v-select>
              </v-col>

              <table>
                <tr style="text-align:center; ">
                  <th style="width:600px; padding: 5px 0 !important">
                    Module
                  </th>
                  <th>Create</th>
                  <th>Edit</th>
                  <th>Delete</th>
                  <th>View</th>
                  <th>Access</th>
                </tr>
                <tr v-for="(items, idx) in permissions" :key="idx">
                  <th>{{ capsTitle(idx) }}</th>
                  <th
                    v-for="(pa, idx) in items"
                    :key="idx"
                    style="text-align:center !important;"
                    class=""
                  >
                    <v-checkbox
                      :value="pa.id"
                      v-model="permission_ids"
                      :hide-details="true"
                      class="pt-0  py-1 chk-align"
                    >
                    </v-checkbox>
                  </th>
                </tr>
              </table>

              <v-col v-if="errors && errors.length > 0" cols="12">
                <ul>
                  <li class="error--text" v-for="(err, i) in errors" :key="i">
                    {{ err }}
                  </li>
                </ul>
              </v-col>

              <v-col>
                <v-btn
                  v-if="can(`assign_permission_edit`)"
                  dark
                  small
                  color="primary"
                  class="mr-4"
                  @click="save"
                >
                  Submit
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-form>
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    role_id: "",
    permission_ids: [],
    permissions: [],
    msg: "",
    snackbar: false,
    Rules: [v => !!v || "This field is required"],
    errors: [],
    roles: []
  }),
  created() {
    this.$axios
      .get("assign-permission/" + this.$route.params.id)
      .then(({ data }) => {
        this.role_id = data.role_id;
        this.permission_ids = data.permission_ids;
      })
      .catch(err => console.log(err));

    this.$axios
      .get("dropDownList")
      .then(({ data }) => {
        this.permissions = data.data;
      })
      .catch(err => console.log(err));

    let options = {
      company_id: this.$auth.user.company.id
    };

    this.$axios
      .get("role", { params: options })
      .then(({ data }) => {
        this.roles = data.data;
      })
      .catch(err => console.log(err));
  },
  methods: {
    capsTitle(val) {
      let res = val;
      let r = res.replace(/[^a-z]/g, " ");
      let title = r.replace(/\b\w/g, c => c.toUpperCase());
      return title;
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
    },
    save() {
      this.errors = [];
      let payload = {
        role_id: this.role_id,
        permission_ids: this.permission_ids
      };
      this.$axios
        .put("assign-permission/" + this.$route.params.id, payload)
        .then(({ data }) => {
          this.msg = "Permissions has been assigned";
          this.snackbar = true;
          setTimeout(() => this.$router.push("/assign_permission"), 2000);
        });
    }
  }
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

th {
  border: 1px solid #dddddd;
  /* text-align: center; */
  padding: 0px 5px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}

.componentWrapper {
  border: solid 1px cadetblue;
  border-radius: 5px;
  padding: 15px 10px 10px;
  width: 95%;
}

.componentWrapper .header {
  position: absolute;
  margin-top: -25px;
  margin-left: 10px;
  color: white;
  background: cadetblue;
  border-radius: 5px;
  padding: 2px 10px;
}

.chk-align {
  text-align: center !important;
  margin-top: 8px !important;
  margin-left: 98px !important;
}
</style>

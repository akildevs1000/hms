<template>
  <div v-if="can(`assign_permission_accesss`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ msg }}
      </v-snackbar>
    </div>
    <!-- v-if="permissions && permissions.length > 0" -->
    <v-card elevation="0">
      <v-form ref="form" lazy-validation>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col>
                <div class="display-1 pa-2">Assign Permissions</div>
              </v-col>
              <v-col>
                <div class="display-1 pa-2 text-right">
                  <v-btn small class="primary" to="/assign_permission">
                    <v-icon small>mdi-arrow-left</v-icon>&nbsp;Back
                  </v-btn>
                </div>
              </v-col>
              <v-col cols="12">
                <v-row>
                  <v-col md="4">
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
                    <span v-if="errors && errors.role_id" class="red--text">
                      {{ errors.role_id[0] }}
                    </span>
                  </v-col>
                </v-row>

                <!-- <v-text-field
                  @input="searchIt"
                  v-model="search"
                  label="Search"
                  single-line
                  hide-details
                ></v-text-field> -->
                <!-- <v-checkbox v-for="(pa, idx) in permissions" :key="idx" :value="pa.id" v-model="permission_ids"
                  :label="`${pa.name}`">
                </v-checkbox> -->
                <!-- <v-checkbox
                  @change="setAllIds"
                  :label="`Select All`"
                  v-model="just_ids"
                >
                </v-checkbox> -->
                <table>
                  <tr style="text-align:center; ">
                    <th style="width:600px; padding: 5px 0 !important">
                      Module
                    </th>
                    <th>Access</th>
                    <th>View</th>
                    <th>Create</th>
                    <th>Edit</th>
                    <th>Delete</th>
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

                <!-- <v-row class="mt-5">
                  <v-col
                    xs="12"
                    sm="12"
                    md="3"
                    cols="12"
                    v-for="(items, idx) in permissions"
                    :key="idx"
                  >
                    <div class="componentWrapper my-3">
                      <div class="header">
                        {{ capsTitle(idx) }}
                      </div>
                      <v-checkbox
                        v-for="(pa, idx) in items"
                        :key="idx"
                        :value="pa.id"
                        v-model="permission_ids"
                        :label="capsTitle(pa.name)"
                        :hide-details="true"
                      >
                      </v-checkbox>
                    </div>
                  </v-col>
                </v-row> -->
              </v-col>
              <v-col cols="12">
                <span v-if="errors && errors.permission_ids" class="red--text">
                  {{ errors.permission_ids[0] }}
                </span>
              </v-col>

              <v-col>
                <v-btn
                  v-if="can(`assign_permission_create`)"
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

      <template v-slot:item.action="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)">
          mdi-pencil
        </v-icon>
        <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
      </template>
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    role_id: "",
    search: "",
    permission_ids: [],
    permissions: [],
    msg: "",
    snackbar: false,
    Rules: [v => !!v || "This field is required"],
    errors: [],
    roles: [],
    just_ids: false
  }),
  created() {
    let options = {
      params: {
        company_id: this.$auth.user.company.id
      }
    };

    this.$axios
      .get("assign-permission/nars", options)
      .then(({ data }) => (this.roles = data))
      .catch(err => console.log(err));

    this.getPermissions();
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
    getPermissions(url = "dropDownList") {
      this.$axios
        .get(url)
        .then(({ data }) => {
          this.permissions = data.data;
        })
        .catch(err => console.log(err));
    },
    setAllIds() {
      this.permission_ids = this.just_ids
        ? this.permissions.map(e => e.id)
        : [];
    },
    searchIt(key) {
      if (key.length == 0) {
        this.getPermissions();
      } else if (key.length > 2) {
        this.getPermissions(`permission/search/${key}`);
      }
      this.permission_ids = [];
    },
    save() {
      this.errors = [];
      let payload = {
        role_id: this.role_id,
        permission_ids: this.permission_ids,
        company_id: this.$auth.user.company.id
      };

      this.$axios.post("assign-permission", payload).then(({ data }) => {
        if (!data.status) {
          this.errors = data.errors;
          return;
        }
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

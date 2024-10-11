<template>
  <div v-if="can(`settings_permissions_access`) && can(`settings_permissions_view`)">
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


              <v-col cols="6">
                <h3>Assign Permissions</h3>
                <div>Dashboard / {{ Module }}
                </div>
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
                    <v-autocomplete :rules="Rules" v-model="role_id" :items="roles" item-value="id" item-text="name"
                      placeholder="Select Role" outlined dense></v-autocomplete>
                    <span v-if="errors && errors.role_id" class="red--text">
                      {{ errors.role_id[0] }}
                    </span>
                  </v-col>
                </v-row>

                <table class="mb-15">
                  <tr style="text-align:center; ">
                    <th style="width:600px;text-align:center; padding: 5px 0 !important">
                      Module
                    </th>
                    <th style="text-align:center;padding-left:50px"> <v-checkbox label="Access"
                        @change="setAllIds('access', $event)">
                      </v-checkbox> </th>
                    <th style="text-align:center;padding-left:50px"> <v-checkbox label="View"
                        @change="setAllIds('view', $event)">
                      </v-checkbox></th>
                    <th style="text-align:center;padding-left:50px"> <v-checkbox label="Create"
                        @change="setAllIds('create', $event)">
                      </v-checkbox></th>
                    <th style="text-align:center;padding-left:50px"> <v-checkbox label="Edit"
                        @change="setAllIds('edit', $event)">
                      </v-checkbox></th>
                    <th style="text-align:center;padding-left:50px"> <v-checkbox label="Delete"
                        @change="setAllIds('delete', $event)">
                      </v-checkbox></th>
                  </tr>
                  <tr v-for="(items, idx) in permissions" :key="idx">
                    <th class="ps-3">{{ capsTitle(idx) }}</th>
                    <th v-for="(pa, idx) in items" :key="idx" style="text-align:center !important;" class="">
                      <v-checkbox :disabled="!can(`settings_permissions_create`)" :value="pa.id" v-model="permission_ids"
                        :hide-details="true" class="pt-0  py-1 chk-align">
                      </v-checkbox>
                    </th>
                  </tr>
                </table>


              </v-col>
              <v-col cols="12">
                <span v-if="errors && errors.permission_ids" class="red--text">
                  {{ errors.permission_ids[0] }}
                </span>
              </v-col>


            </v-row>
          </v-container>
          <v-card-actions>

            <v-spacer></v-spacer>
            <v-btn v-if="can(`settings_permissions_create`)" dark small color="primary" class="mr-4" @click="save">
              Submit
            </v-btn>
          </v-card-actions>
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
    Module: 'Permisions',
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



    // if (this.$route.params.id) {
    //   this.role_id = this.$route.params.id;
    // }
    let options = {
      params: {
        company_id: this.$auth.user.company.id
      }
    };

    this.$axios
      .get("assign-permission/nars", options)
      .then(({ data }) => {
        this.roles = data;
        this.role_id = this.roles[0].id;

      })
      .catch(err => console.log(err));

    this.getPermissions();
  },
  methods: {
    capsTitle(val) {
      if (val == 'gst') { val = val.toUpperCase(); return val; }
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
    setAllIds(filter, e) {
      let isSelected = e;

      // this.permission_ids = this.just_ids
      //   ? this.permissions.map(e => e.id)
      //   : [];

      // Function to filter IDs containing the text "edit"
      let data = this.permissions;
      const filteredIds = [];

      // Loop through each module in the data
      for (const module in data) {
        if (data.hasOwnProperty(module)) {
          // Filter the items in the module where the "name" contains "edit"
          const editItems = data[module].filter(item => item.name.includes(filter));

          // Extract and store the IDs from the filtered items
          const editItemIds = editItems.map(item => item.id);

          if (isSelected)
            this.permission_ids.push(...editItemIds);
          else {

            const indexToDelete = this.permission_ids.findIndex(item => item === editItemIds[0]);

            if (indexToDelete !== -1) {
              this.permission_ids.splice(indexToDelete, 1);

            }
          }
        }
      }
      const uniqueSet = new Set(this.permission_ids);
      this.permission_ids = Array.from(uniqueSet);


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
        setTimeout(() => this.$router.push("/assign_permission"), 1000);
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

tr:nth-child(even) {
  background-color: #e9e9e9;
}

th,
td {
  border: 1px solid #dddddd;
  /* text-align: center; */
  padding: 5px 5px;
}

.chk-align {
  text-align: center !important;
  margin-top: 8px !important;
  margin-left: 98px !important;
}

* {
  box-sizing: border-box;
}

body>div {
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

table>thead {
  background-color: #00bcd4;
  color: #fff;
}

table>thead th {
  padding: 15px;
}

table th,
table td {
  border: 1px solid #00000017;
  padding: 0px 0px;
}

table>tbody>tr>td>img {
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

.action_btn>a {
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

.action_btn>a:nth-child(1) {
  border-color: #26a69a;
}

.action_btn>a:nth-child(2) {
  border-color: orange;
}

.action_btn>a:hover {
  box-shadow: 0 3px 8px #0003;
}

table>tbody>tr {
  background-color: #fff;
  transition: 0.3s ease-in-out;
}

table>tbody>tr:nth-child(even) {
  background-color: rgb(238, 238, 238);
}

table>tbody>tr:hover {
  filter: drop-shadow(0px 2px 6px #0002);
}
</style>

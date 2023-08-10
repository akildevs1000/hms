<template>
  <div v-if="can('settings_users_access') && can('settings_users_view')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-dialog v-model="userDialog" max-width="50%">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <v-toolbar color="background" dense flat dark>
            <span>{{ formTitle }} {{ Model }}</span>
          </v-toolbar>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="userDialog = false">mdi mdi-close-box</v-icon>
        </v-toolbar>
        <v-container>
          <v-row>
            <v-col md="12" lg="12">
              <v-card elevation="0">
                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col md="8">
                        <v-row>
                          <v-col md="3" cols="12" sm="12">
                            <v-select v-model="editedItem.title" :items="titleItems" dense item-text="name"
                              item-value="name" :hide-details="errors && !errors.title" :error="errors && errors.title"
                              :error-messages="errors && errors.title ?
                                errors.title[0] : ''
                                " outlined></v-select>
                          </v-col>
                          <v-col md="9" cols="12">
                            <v-text-field v-model="editedItem.name" placeholder="Name" label="Name" outlined
                              :hide-details="true" dense></v-text-field>
                            <span v-if="errors && errors.name" class="error--text">{{ errors.name[0]
                            }}</span>
                          </v-col>
                          <v-col md="12" cols="12">
                            <v-text-field v-model="editedItem.email" placeholder="Email" label="Email" outlined
                              :hide-details="true" type="email" dense></v-text-field>
                            <span v-if="errors && errors.email" class="error--text">{{ errors.email[0]
                            }}</span>
                          </v-col>
                          <v-col md="12" cols="12">
                            <v-text-field v-model="editedItem.password" placeholder="Password" label="Password" outlined
                              :hide-details="true" type="password" dense></v-text-field>
                            <span v-if="errors && errors.password" class="error--text">{{ errors.password[0]
                            }}</span>
                          </v-col>
                          <v-col md="12" cols="12">
                            <v-text-field v-model="editedItem.password_confirmation" placeholder="Confirm Password"
                              label="Confirm Password" outlined :hide-details="true" type="password" dense></v-text-field>
                            <span v-if="errors && errors.password_confirmation" class="error--text">{{
                              errors.password_confirmation[0]
                            }}</span>
                          </v-col>
                          <v-col md="12" cols="12">
                            <v-text-field v-model="editedItem.mobile" placeholder="Mobile" label="Mobile" outlined
                              :hide-details="true" type="number" dense></v-text-field>
                            <span v-if="errors && errors.mobile" class="error--text">{{ errors.mobile[0]
                            }}</span>
                          </v-col>
                          <v-col md="12" cols="12">

                            <v-select :items="roles" item-text="name" item-value="id" v-model="editedItem.role_id"
                              outlined placeholder="Select Role" lable="Role" :hide-details="true" dense></v-select>
                            <span v-if="errors && errors.role_id" class="error--text">{{ errors.role_id[0] }}</span>

                            <v-select v-model="editedItem.is_active" :items="[
                              { name: 'Active', value: '1' },
                              { name: 'Inactive', value: '0' }
                            ]" dense item-text="name" item-value="value" :hide-details="errors && !errors.is_active"
                              :error="errors && errors.is_active" :error-messages="errors && errors.is_active ?
                                errors.is_active[0] : ''
                                " outlined></v-select>

                            <span v-if="errors && errors.is_active" class="error--text">{{ errors.is_active[0]
                            }}</span>

                          </v-col>
                        </v-row>
                      </v-col> <v-col md="4">
                        <div class="form-group" style="margin: 0 auto; width: 200px">
                          <v-img style="
                            width: 100%;
                            height: 200px;
                            border: 1px solid #5fafa3;
                            border-radius: 50%;
                            margin: 0 auto;
                    " :src="previewImage || '/no-profile-image.jpg'"></v-img>
                          <br />
                          <v-btn small class="form-control primary" @click="onpick_attachment">{{ !upload.name ? "Upload"
                            :
                            "Change" }} Profile Image
                            <v-icon right dark>mdi-cloud-upload</v-icon>
                          </v-btn>
                          <input required type="file" @change="attachment" style="display: none" accept="image/*"
                            ref="attachment_input" />

                          <span v-if="errors && errors.profile_picture" class="text-danger mt-2">{{
                            errors.profile_picture[0]
                          }}</span>
                        </div>
                      </v-col>
                    </v-row>

                    <v-card-actions>
                      <v-btn class="error" @click="userDialog = false">
                        Cancel
                      </v-btn>
                      <v-btn class="primary" @click="save">Save</v-btn>
                    </v-card-actions>

                  </v-container>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>
    <v-toolbar class="rounded-md" color="background" dense flat dark>
      <span> {{ Model }} List</span>
      <v-spacer></v-spacer>
      <v-btn class="float-right py-3" @click="toggleFilter" x-small color="primary">
        <v-icon color="white" small class="py-5">mdi-magnify</v-icon>
        Search
      </v-btn>
      <v-btn v-if="can('settings_users_create')" class="float-right py-3 ml-2" @click="userDialog = true" x-small
        color="primary">
        <v-icon color="white" small class="py-5">mdi-plus</v-icon>
        Add
      </v-btn>
    </v-toolbar>

    <v-data-table :headers="headers" :items="userData" :options.sync="options" :server-items-length="totalUserData"
      :loading="loading" class="elevation-1" :footer-props="{

        itemsPerPageOptions: [10, 50, 100, 500, 1000],

      }">
      <template v-slot:header="{ props: { headers } }">
        <tr v-if="isFilter">
          <th v-for="header in headers" :key="header.text">
            <v-text-field v-if="header.filterable" v-model="filters[header.value]" :label="header.text" clearable
              @input="applyFilters" dense outlined flat append-icon="mdi-magnify"></v-text-field>
            <!-- <template v-else>
              {{ header.text }}
            </template> -->
          </th>
        </tr>
      </template>
      <template v-slot:item.role="{ item }">
        {{ item.role && capsTitle(item.role.name) }}

      </template>
      <template v-slot:item.actions="{ item }">

        <v-menu bottom left v-if="can('settings_users_edit') || can('settings_users_delete')">
          <template v-slot:activator="{ on, attrs }">
            <v-btn dark-2 icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list width="120" dense>
            <v-list-item v-if="can('settings_users_edit')" @click="editItem(item)">
              <v-list-item-title style="cursor: pointer">
                <v-icon color="secondary" small>
                  mdi-pencil
                </v-icon>
                Edit
              </v-list-item-title>
            </v-list-item>
            <v-list-item v-if="can('settings_users_delete')" @click="deleteItem(item)">
              <v-list-item-title style="cursor: pointer">
                <v-icon color="error" small> mdi-delete </v-icon>
                Delete
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>



    </v-data-table>
  </div>
  <NoAccess v-else />
</template>


<script>
export default {
  data() {
    return {
      Rules: [v => !!v || "This field is required"],
      roles: [],
      Model: "Users",
      snackbar: false,
      response: "",
      userDialog: false,
      isFilter: false,
      totalUserData: 0,
      userData: [],
      loading: false,
      options: {},
      filters: {},
      headers: [
        // {
        //   text: 'Dessert (100g serving)',
        //   align: 'start',
        //   sortable: false,
        //   value: 'name',
        // },
        { text: 'Title', value: 'title', filterable: true },
        { text: 'Name', value: 'name', filterable: true },
        { text: 'Email', value: 'email', filterable: true },
        { text: 'Mobile', value: 'mobile', filterable: true },
        { text: 'Role', value: 'role', filterable: false },
        { text: 'Actions', value: 'actions', filterable: false, sortable: false },
      ],
      editedIndex: -1,
      editedItem: {
        title: "",
        name: "",
        password: "",
        password_confirmation: "",
        email: "",
        mobile: "",
        is_active: 1,
      },

      defaultItem: {
        title: "",
        name: "",
        password: "",
        password_confirmation: "",
        email: "",
        mobile: "",
      },

      upload: {
        name: "",
      },
      previewImage: null,

      titleItems: [
        { id: 1, name: "Mr" },
        { id: 2, name: "Mrs" },
        { id: 3, name: "Miss" },
        { id: 4, name: "Ms" },
        { id: 5, name: "Dr" },
      ],
      selectedFile: "",
      errors: [],
    }
  },
  watch: {
    userDialog(val) {
      !val ? this.editedItem = {} : ''
    },

    options: {
      handler() {
        this.getDataFromApi()
      },
      deep: true,
    },
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },

  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },
    capsTitle(val) {
      let res = val;
      let r = res.replace(/[^a-z]/g, " ");
      let title = r.replace(/\b\w/g, c => c.toUpperCase());
      return title;
    },
    toggleFilter() {
      this.isFilter = !this.isFilter;
    },

    applyFilters() {
      this.getDataFromApi();
    },

    editItem(item) {
      this.editedIndex = this.userData.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.previewImage = item.image;
      this.userDialog = true;
    },

    deleteItem() {
      console.log('deleteItem');
    },

    onpick_attachment() {
      this.$refs.attachment_input.click();
    },
    attachment(e) {
      this.upload.name = e.target.files[0] || "";

      let input = this.$refs.attachment_input;
      let file = input.files;

      if (file[0].size > 1024 * 1024) {
        e.preventDefault();
        this.errors["profile_picture"] = [
          "File too big (> 1MB). Upload less than 1MB",
        ];
        return;
      }

      if (file && file[0]) {
        let reader = new FileReader();
        reader.onload = (e) => {
          this.previewImage = e.target.result;
          this.selectedFile = event.target.result;
          this.$refs.cropper.replace(this.selectedFile);
        };
        reader.readAsDataURL(file[0]);
        this.$emit("input", file[0]);

        this.dialogCropping = true;
      }
    },

    getDataFromApi() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;
      let sortedBy = sortBy[0];
      let sortedDesc = sortDesc[0]

      // Make an API call to retrieve the userData from Laravel backend using pagination

      this.$axios
        .get('users', {
          params: {
            page: page,
            itemsPerPage: itemsPerPage,
            sortBy: sortedBy,
            sortDesc: sortedDesc,
            company_id: this.$auth.user.company.id,
            ...this.filters
          },
        })
        .then((response) => {
          this.userData = response.data.data;
          this.totalUserData = response.data.total;
          this.loading = false;
        })
        .catch((error) => {
          console.error(error);
          this.loading = false;
        });


      this.getRolesList();
    },
    getRolesList() {
      let options = { params: { company_id: this.$auth.user.company.id } };
      this.$axios.get('role', options).then((data) => {
        this.roles = data.data.data;
        console.log(this.roles);
      });
    },
    save() {
      let payload = new FormData();

      if (this.upload.name) {
        payload.append("image", this.upload.name);
      }

      if (this.editedItem.mobile) {
        payload.append("mobile", this.editedItem.mobile);
      }

      if (this.editedItem.password) {
        payload.append("password", this.editedItem.password);
      }

      if (this.editedItem.password_confirmation) {
        payload.append("password_confirmation", this.editedItem.password_confirmation);
      }
      if (this.editedItem.role_id) {
        payload.append("role_id", this.editedItem.role_id);
        //payload.append("employee_role_id", this.editedItem.role_id);
      }


      payload.append("title", this.editedItem.title);
      payload.append("name", this.editedItem.name);
      payload.append("email", this.editedItem.email);
      payload.append("is_active", this.editedItem.is_active);

      payload.append("company_id", this.$auth.user.company.id);


      if (this.editedIndex > -1) {
        payload.append("_method", 'PUT');

        this.$axios
          .post('users' + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.userDialog = false;
              this.editedItem = {};
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post('users', payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.editedItem = {};
              this.userDialog = false;
              this.snackbar = data.status;
              this.response = data.message;
              this.userDialog = false;
              this.errors = [];
              this.search = "";
            }
          })
          .catch((res) => console.log(res));
      }

    },


  },
}
</script>

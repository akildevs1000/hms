<template>
  <div v-if="can('settings_users_access') && can('settings_users_view')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-dialog v-model="userDialog" max-width="700">
      <v-card>
        <v-toolbar class="rounded-md" color="blue" dense flat dark>
          <span>{{ editedItem.id ? "Edit" : "Create" }} {{ Model }}</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="userDialog = false"
            >mdi-close</v-icon
          >
        </v-toolbar>
        <v-card-text>
          <v-container v-if="!editedItem.id">
            <v-row>
              <v-col md="8">
                <v-row>
                  <v-col md="6" cols="12" sm="12">
                    <v-autocomplete
                      v-model="editedItem.title"
                      :items="titleItems"
                      dense
                      item-text="name"
                      item-value="name"
                      :hide-details="errors && !errors.title"
                      :error="errors && errors.title"
                      :error-messages="
                        errors && errors.title ? errors.title[0] : ''
                      "
                      outlined
                    ></v-autocomplete>
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-autocomplete
                      :items="roles"
                      item-text="name"
                      item-value="id"
                      v-model="editedItem.role_id"
                      outlined
                      placeholder="Select Role"
                      label="Role"
                      :hide-details="true"
                      dense
                    ></v-autocomplete>
                    <span v-if="errors && errors.role_id" class="error--text">{{
                      errors.role_id[0]
                    }}</span>
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-text-field
                      v-model="editedItem.name"
                      placeholder="Name"
                      label="Name"
                      outlined
                      :hide-details="true"
                      dense
                    ></v-text-field>
                    <span v-if="errors && errors.name" class="error--text">{{
                      errors.name[0]
                    }}</span>
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-text-field
                      v-model="editedItem.last_name"
                      placeholder="Last Name"
                      label="last Name"
                      outlined
                      :hide-details="true"
                      dense
                    ></v-text-field>
                    <span
                      v-if="errors && errors.last_name"
                      class="error--text"
                      >{{ errors.last_name[0] }}</span
                    >
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-text-field
                      v-model="editedItem.email"
                      placeholder="Email"
                      label="Email"
                      outlined
                      :hide-details="true"
                      type="email"
                      dense
                    ></v-text-field>
                    <span v-if="errors && errors.email" class="error--text">{{
                      errors.email[0]
                    }}</span>
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-text-field
                      v-model="editedItem.mobile"
                      placeholder="Mobile"
                      label="Mobile"
                      outlined
                      :hide-details="true"
                      type="number"
                      dense
                    ></v-text-field>
                    <span v-if="errors && errors.mobile" class="error--text">{{
                      errors.mobile[0]
                    }}</span>
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-text-field
                      v-model="editedItem.password"
                      placeholder="Password"
                      autocomplete="false"
                      label="Password"
                      outlined
                      :hide-details="true"
                      type="password"
                      dense
                    ></v-text-field>
                    <span
                      v-if="errors && errors.password"
                      class="error--text"
                      >{{ errors.password[0] }}</span
                    >
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-text-field
                      v-model="editedItem.password_confirmation"
                      autocomplete="false"
                      placeholder="Confirm Password"
                      label="Confirm Password"
                      outlined
                      :hide-details="true"
                      type="password"
                      dense
                    ></v-text-field>
                    <span
                      v-if="errors && errors.password_confirmation"
                      class="error--text"
                      >{{ errors.password_confirmation[0] }}</span
                    >
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-autocomplete
                      label="Status"
                      v-model="editedItem.is_active"
                      :items="[
                        { name: 'Active', value: '1' },
                        { name: 'Inactive', value: '0' },
                      ]"
                      dense
                      item-text="name"
                      item-value="value"
                      :hide-details="errors && !errors.is_active"
                      :error="errors && errors.is_active"
                      :error-messages="
                        errors && errors.is_active ? errors.is_active[0] : ''
                      "
                      outlined
                    ></v-autocomplete>
                    <span
                      v-if="errors && errors.is_active"
                      class="error--text"
                      >{{ errors.is_active[0] }}</span
                    >
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-autocomplete
                      label="OTP"
                      v-model="editedItem.enable_whatsapp_otp"
                      :items="[
                        { name: 'Enable', value: 1 },
                        { name: 'Disable', value: 0 },
                      ]"
                      dense
                      item-text="name"
                      item-value="value"
                      :hide-details="errors && !errors.enable_whatsapp_otp"
                      :error="errors && errors.enable_whatsapp_otp"
                      :error-messages="
                        errors && errors.enable_whatsapp_otp
                          ? errors.enable_whatsapp_otp[0]
                          : ''
                      "
                      outlined
                    ></v-autocomplete>
                    <span
                      v-if="errors && errors.enable_whatsapp_otp"
                      class="error--text"
                      >{{ errors.enable_whatsapp_otp[0] }}</span
                    >
                  </v-col>

                  <v-col cols="12" class="text-right">
                    <v-btn
                      small
                      class="grey white--text"
                      @click="userDialog = false"
                    >
                      Cancel
                    </v-btn>
                    <v-btn small class="primary" @click="save">Save</v-btn>
                  </v-col>
                </v-row>
              </v-col>
              <v-col md="4">
                <div class="form-group" style="margin: 0 auto; width: 150px">
                  <v-img
                    style="
                      width: 100%;
                      height: 150px;
                      border: 1px solid #4390fc;
                      border-radius: 50%;
                      margin: 0 auto;
                    "
                    :src="previewImage || '/no-profile-image.jpg'"
                  ></v-img>
                  <br />
                  <v-btn
                    small
                    class="form-control primary"
                    @click="onpick_attachment"
                    >{{ !upload.name ? "Upload" : "Change" }}
                    Image
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
                    class="error--text mt-2"
                    >{{ errors.profile_picture[0] }}</span
                  >
                </div>
              </v-col>
            </v-row>
          </v-container>
          <v-tabs v-else>
            <v-tab>Profile</v-tab>
            <v-tab>Document</v-tab>
            <v-tab-item>
              <v-container>
                <v-row>
                  <v-col md="8">
                    <v-row>
                      <v-col md="6" cols="12" sm="12">
                        <v-autocomplete
                          v-model="editedItem.title"
                          :items="titleItems"
                          dense
                          item-text="name"
                          item-value="name"
                          :hide-details="errors && !errors.title"
                          :error="errors && errors.title"
                          :error-messages="
                            errors && errors.title ? errors.title[0] : ''
                          "
                          outlined
                        ></v-autocomplete>
                      </v-col>
                      <v-col md="6" cols="12">
                        <v-autocomplete
                          :items="roles"
                          item-text="name"
                          item-value="id"
                          v-model="editedItem.role_id"
                          outlined
                          placeholder="Select Role"
                          label="Role"
                          :hide-details="true"
                          dense
                        ></v-autocomplete>
                        <span
                          v-if="errors && errors.role_id"
                          class="error--text"
                          >{{ errors.role_id[0] }}</span
                        >
                      </v-col>
                      <v-col md="6" cols="12">
                        <v-text-field
                          v-model="editedItem.name"
                          placeholder="Name"
                          label="Name"
                          outlined
                          :hide-details="true"
                          dense
                        ></v-text-field>
                        <span
                          v-if="errors && errors.name"
                          class="error--text"
                          >{{ errors.name[0] }}</span
                        >
                      </v-col>
                      <v-col md="6" cols="12">
                        <v-text-field
                          v-model="editedItem.last_name"
                          placeholder="Last Name"
                          label="last Name"
                          outlined
                          :hide-details="true"
                          dense
                        ></v-text-field>
                        <span
                          v-if="errors && errors.last_name"
                          class="error--text"
                          >{{ errors.last_name[0] }}</span
                        >
                      </v-col>
                      <v-col md="6" cols="12">
                        <v-text-field
                          v-model="editedItem.email"
                          placeholder="Email"
                          label="Email"
                          outlined
                          :hide-details="true"
                          type="email"
                          dense
                        ></v-text-field>
                        <span
                          v-if="errors && errors.email"
                          class="error--text"
                          >{{ errors.email[0] }}</span
                        >
                      </v-col>
                      <v-col md="6" cols="12">
                        <v-text-field
                          v-model="editedItem.mobile"
                          placeholder="Mobile"
                          label="Mobile"
                          outlined
                          :hide-details="true"
                          type="number"
                          dense
                        ></v-text-field>
                        <span
                          v-if="errors && errors.mobile"
                          class="error--text"
                          >{{ errors.mobile[0] }}</span
                        >
                      </v-col>
                      <v-col md="6" cols="12">
                        <v-text-field
                          v-model="editedItem.password"
                          placeholder="Password"
                          autocomplete="false"
                          label="Password"
                          outlined
                          :hide-details="true"
                          type="password"
                          dense
                        ></v-text-field>
                        <span
                          v-if="errors && errors.password"
                          class="error--text"
                          >{{ errors.password[0] }}</span
                        >
                      </v-col>
                      <v-col md="6" cols="12">
                        <v-text-field
                          v-model="editedItem.password_confirmation"
                          autocomplete="false"
                          placeholder="Confirm Password"
                          label="Confirm Password"
                          outlined
                          :hide-details="true"
                          type="password"
                          dense
                        ></v-text-field>
                        <span
                          v-if="errors && errors.password_confirmation"
                          class="error--text"
                          >{{ errors.password_confirmation[0] }}</span
                        >
                      </v-col>
                      <v-col md="6" cols="12">
                        <v-autocomplete
                          label="Status"
                          v-model="editedItem.is_active"
                          :items="[
                            { name: 'Active', value: '1' },
                            { name: 'Inactive', value: '0' },
                          ]"
                          dense
                          item-text="name"
                          item-value="value"
                          :hide-details="errors && !errors.is_active"
                          :error="errors && errors.is_active"
                          :error-messages="
                            errors && errors.is_active
                              ? errors.is_active[0]
                              : ''
                          "
                          outlined
                        ></v-autocomplete>
                        <span
                          v-if="errors && errors.is_active"
                          class="error--text"
                          >{{ errors.is_active[0] }}</span
                        >
                      </v-col>
                      <v-col md="6" cols="12">
                        <v-autocomplete
                          label="OTP"
                          v-model="editedItem.enable_whatsapp_otp"
                          :items="[
                            { name: 'Enable', value: 1 },
                            { name: 'Disable', value: 0 },
                          ]"
                          dense
                          item-text="name"
                          item-value="value"
                          :hide-details="errors && !errors.enable_whatsapp_otp"
                          :error="errors && errors.enable_whatsapp_otp"
                          :error-messages="
                            errors && errors.enable_whatsapp_otp
                              ? errors.enable_whatsapp_otp[0]
                              : ''
                          "
                          outlined
                        ></v-autocomplete>
                        <span
                          v-if="errors && errors.enable_whatsapp_otp"
                          class="error--text"
                          >{{ errors.enable_whatsapp_otp[0] }}</span
                        >
                      </v-col>

                      <v-col cols="12" class="text-right">
                        <v-btn
                          small
                          class="grey white--text"
                          @click="userDialog = false"
                        >
                          Cancel
                        </v-btn>
                        <v-btn small class="primary" @click="save">Save</v-btn>
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col md="4">
                    <div
                      class="form-group"
                      style="margin: 0 auto; width: 150px"
                    >
                      <v-img
                        style="
                          width: 100%;
                          height: 150px;
                          border: 1px solid #4390fc;
                          border-radius: 50%;
                          margin: 0 auto;
                        "
                        :src="previewImage || '/no-profile-image.jpg'"
                      ></v-img>
                      <br />
                      <v-btn
                        small
                        class="form-control primary"
                        @click="onpick_attachment"
                        >{{ !upload.name ? "Upload" : "Change" }}
                        Image
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
                        class="error--text mt-2"
                        >{{ errors.profile_picture[0] }}</span
                      >
                    </div>
                  </v-col>
                </v-row>
              </v-container>
            </v-tab-item>
            <v-tab-item>
              <EmployeeDocument :employee_id="editedItem.id" />
            </v-tab-item>
          </v-tabs>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-toolbar class="rounded-md" dense flat>
      <v-toolbar-title
        ><span> {{ Model }}s</span></v-toolbar-title
      >

      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
          >
            <v-icon class="ml-2" @click="reload()" dark>mdi-reload</v-icon>
          </v-btn>
        </template>
        <span>Reload</span>
      </v-tooltip>
      <!-- <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
            @click="toggleFilter"
          >
            <v-icon>mdi-filter</v-icon>
          </v-btn>
        </template>
        <span>Filter</span>
      </v-tooltip> -->
      <v-spacer></v-spacer>

      <v-tooltip top color="primary">
        <template
          v-if="can('lost_and_found_create')"
          v-slot:activator="{ on, attrs }"
        >
          <v-btn class="blue" dark small v-bind="attrs" v-on="on" @click="addNewItem">
            <v-icon small center>mdi-plus</v-icon> Employee
          </v-btn>
        </template>
        <span>Add New Record</span>
      </v-tooltip>
    </v-toolbar>

    <v-data-table
      :headers="headers"
      :items="userData"
      :options.sync="options"
      :server-items-length="totalUserData"
      :loading="loading"
      :footer-props="{
        itemsPerPageOptions: [10, 50, 100, 500, 1000],
      }"
    >
      <template v-slot:header="{ props: { headers } }">
        <tr v-if="isFilter">
          <td v-for="header in headers" :key="header.text">
            <v-text-field
              clearable
              :hide-details="true"
              v-if="header.filterable && !header.filterSpecial"
              v-model="filters[header.key]"
              :id="header.value"
              @input="applyFilters(header.key, $event)"
              outlined
              dense
              autocomplete="off"
            ></v-text-field>
            <v-autocomplete
              v-else-if="header.filterable && header.key == 'status'"
              clearable
              @click:clear="
                filters[header.value] = '';
                applyFilters();
              "
              :hide-details="true"
              @change="applyFilters('status', $event)"
              item-value="value"
              item-text="title"
              v-model="filters[header.value]"
              outlined
              dense
              :items="[
                { value: '', title: 'All' },
                { value: '1', title: 'Active' },
                {
                  value: '0',
                  title: 'In-Active',
                },
              ]"
              placeholder="Status"
            ></v-autocomplete>
            <v-autocomplete
              v-else-if="header.filterable && header.key == 'role_id'"
              clearable
              @click:clear="
                filters[header.key] = '';
                applyFilters();
              "
              :hide-details="true"
              @change="applyFilters('status', $event)"
              item-value="id"
              item-text="name"
              v-model="filters[header.key]"
              outlined
              dense
              :items="roles"
              placeholder="Role"
            ></v-autocomplete>
          </td>
        </tr>
      </template>

      <template v-slot:item.photo="{ item }">
        <v-img
          style="
            border-radius: 50%;
            height: 100px;
            width: 100px;
            margin: 0 auto;
          "
          :src="item.image || '/no-image.PNG'"
        >
        </v-img>
      </template>

      <template v-slot:item.name="{ item }">
        {{ item.title }} {{ item.name }} {{ item.last_name }}
      </template>
      <template v-slot:item.role.name="{ item }">
        {{ item.role && item.role.name }}
      </template>
      <template v-slot:item.is_active="{ item }">
        {{ item.is_active == 1 ? "Active" : "In-Active" }}
      </template>
      <template v-slot:item.enable_whatsapp_otp="{ item }">
        {{ item.enable_whatsapp_otp == 1 ? "Active" : "In-Active" }}
      </template>

      <template v-slot:item.actions="{ item }">
        <v-menu
          bottom
          left
          v-if="can('settings_users_edit') || can('settings_users_delete')"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn dark-2 icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>

          <v-list width="120" dense>
            <v-list-item
              v-if="can('settings_users_edit')"
              @click="editItem(item)"
            >
              <v-list-item-title style="cursor: pointer">
                <v-icon color="secondary" small> mdi-pencil </v-icon>
                Edit
              </v-list-item-title>
            </v-list-item>
            <v-list-item
              v-if="can('settings_users_delete')"
              @click="deleteItem(item)"
            >
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
      Rules: [(v) => !!v || "This field is required"],
      roles: [],
      Model: "Employee",
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
        {
          text: "Photo",
          value: "photo",
          sortable: false,
          filterable: false,
          align: "center",
        },

        { text: "Name", value: "name", key: "name", filterable: true },
        { text: "Email", value: "email", key: "email", filterable: true },
        { text: "Mobile", value: "mobile", key: "mobile", filterable: true },
        {
          text: "Role",
          value: "role.name",
          key: "role_id",
          filterable: true,
          filterSpecial: true,
        },
        {
          text: "OTP",
          value: "enable_whatsapp_otp",
          key: "enable_whatsapp_otp",
          key: "status",
          filterable: true,
          filterSpecial: true,
        },
        {
          text: "Status",
          value: "is_active",
          key: "is_active",
          filterable: true,
          key: "status",
          filterSpecial: true,
        },
        {
          text: "Actions",
          value: "actions",
          filterable: false,
          sortable: false,
        },
      ],
      editedIndex: -1,
      editedItem: {
        title: "Mr",
        name: "",
        password: "",
        password_confirmation: "",
        email: "",
        mobile: "",
        is_active: 1,
        enable_whatsapp_otp: 1,
        last_name: "",
      },

      defaultItem: {
        title: "",
        name: "",
        password: "",
        password_confirmation: "",
        email: "",
        mobile: "",
        is_active: 1,
        enable_whatsapp_otp: 1,
        last_name: "",
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
    };
  },
  watch: {
    // userDialog(val) {
    //   !val ? (this.editedItem = []) : "";
    // },

    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },
  created() {
    this.getRolesList();
  },

  methods: {
    reload() {
      this.isFilter = false;
      this.filters = {};
      this.$set(this.options, "page", 1);
      this.getDataFromApi(this.endpoint, 1);
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    capsTitle(val) {
      let res = val;
      let r = res.replace(/[^a-z]/g, " ");
      let title = r.replace(/\b\w/g, (c) => c.toUpperCase());
      return title;
    },
    toggleFilter() {
      this.isFilter = !this.isFilter;
    },

    applyFilters() {
      this.getDataFromApi();
    },

    addNewItem () {
      this.userDialog = true;
      this.errors = [];
      this.editedItem = {};
    },
    editItem(item) {
      this.errors = [];
      this.editedIndex = this.userData.indexOf(item);
      this.editedItem = Object.assign({}, item);

      if (this.editedItem.title == null || this.editedItem.title == "") {
        this.editedItem.title = "Mr";
      }
      if (this.editedItem.last_name == null) {
        this.editedItem.last_name = "";
      }
      this.previewImage = item.image;
      this.userDialog = true;
    },

    deleteItem() {
      console.log("deleteItem");
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
      let sortedDesc = sortDesc[0];

      // Make an API call to retrieve the userData from Laravel backend using pagination

      this.$axios
        .get("users", {
          params: {
            page: page,
            itemsPerPage: itemsPerPage,
            sortBy: sortedBy,
            sortDesc: sortedDesc,
            company_id: this.$auth.user.company.id,
            ...this.filters,
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
    },
    getRolesList() {
      let options = { params: { company_id: this.$auth.user.company.id } };
      this.$axios.get("role", options).then((data) => {
        this.roles = data.data.data;
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
        payload.append(
          "password_confirmation",
          this.editedItem.password_confirmation
        );
      }
      if (this.editedItem.role_id) {
        payload.append("role_id", this.editedItem.role_id);
        //payload.append("employee_role_id", this.editedItem.role_id);
      }
      if (this.editedItem.title) payload.append("title", this.editedItem.title);
      payload.append("name", this.editedItem.name);
      payload.append("email", this.editedItem.email);
      payload.append("is_active", this.editedItem.is_active);

      payload.append("company_id", this.$auth.user.company.id);
      payload.append(
        "enable_whatsapp_otp",
        this.editedItem.enable_whatsapp_otp
      );
      payload.append("last_name", this.editedItem.last_name);

      if (this.editedIndex > -1) {
        payload.append("_method", "PUT");

        this.$axios
          .post("users" + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.error = true;
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
          .post("users", payload)
          .then(({ data }) => {
            if (!data.status) {
              this.error = true;
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
};
</script>

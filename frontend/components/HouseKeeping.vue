<template>
  <div v-if="can('employee_access') && can('employee_view')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-dialog v-model="userDialog" max-width="700">
      <AssetsIconClose left="690" @click="userDialog = false" />
      <v-card>
        <v-alert class="grey lighten-3" dense flat>
          <span>{{ editedItem.id ? "Edit" : "Create" }}</span>
        </v-alert>
        <v-card-text>
          <v-container v-if="!editedItem.id">
            <v-row>
              <v-col md="8">
                <v-row>
                  <v-col cols="12">
                    <v-autocomplete
                      v-model="editedItem.title"
                      :items="titleItems"
                      dense
                      item-text="name"
                      item-value="name"
                      :hide-details="errors && !errors.title"
                      :error-messages="
                        errors && errors.title ? errors.title[0] : ''
                      "
                      outlined
                    ></v-autocomplete>
                  </v-col>

                  <v-col cols="12">
                    <v-text-field
                      v-model="editedItem.name"
                      placeholder="First Name"
                      label="First Name"
                      outlined
                      :hide-details="true"
                      dense
                    ></v-text-field>
                    <span v-if="errors && errors.name" class="error--text">{{
                      errors.name[0]
                    }}</span>
                  </v-col>
                  <v-col cols="12">
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

                  <v-col cols="12">
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
                  <v-col cols="12">
                    <v-text-field
                      v-model="editedItem.pin"
                      placeholder="Pin"
                      autocomplete="false"
                      label="Pin"
                      outlined
                      :hide-details="true"
                      dense
                    ></v-text-field>
                    <span v-if="errors && errors.pin" class="error--text">{{
                      errors.pin[0]
                    }}</span>
                  </v-col>
                  <v-col cols="12">
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

                  <v-col cols="12" class="text-center">
                    <AssetsButton
                      :options="{
                        label: `Cancel`,
                        icon: ``,
                        color: `red`,
                      }"
                      @click="userDialog = false"
                    />
                    <AssetsButton
                      :options="{
                        label: `Submit`,
                        icon: ``,
                        color: `blue`,
                      }"
                      @click="save"
                    />
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
                      <v-col cols="12">
                        <v-autocomplete
                          label="Title"
                          v-model="editedItem.title"
                          :items="titleItems"
                          dense
                          item-text="name"
                          item-value="name"
                          :hide-details="errors && !errors.title"
                          :error-messages="
                            errors && errors.title ? errors.title[0] : ''
                          "
                          outlined
                        ></v-autocomplete>
                      </v-col>
                      <v-col cols="12">
                        <v-text-field
                          v-model="editedItem.name"
                          placeholder="First Name"
                          label="First Name"
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
                      <v-col cols="12">
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
                      <v-col cols="12">
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

                      <v-col cols="12">
                        <v-text-field
                          v-model="editedItem.pin"
                          placeholder="Pin"
                          autocomplete="false"
                          label="Pin"
                          outlined
                          :hide-details="true"
                          dense
                        ></v-text-field>
                        <span v-if="errors && errors.pin" class="error--text">{{
                          errors.pin[0]
                        }}</span>
                      </v-col>

                      <v-col cols="12">
                        <v-text-field
                          v-model="editedItem.device_id"
                          placeholder="Mobile Device Key"
                          autocomplete="false"
                          label="Mobile Device Key"
                          outlined
                          :hide-details="true"
                          dense
                        ></v-text-field>
                        <span v-if="errors && errors.device_id" class="error--text">{{
                          errors.device_id[0]
                        }}</span>
                      </v-col>

                      <v-col cols="12">
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

                      <v-col cols="12" class="text-right">
                        <!-- <v-btn
                          small
                          class="grey white--text"
                          @click="userDialog = false"
                        >
                          Cancel
                        </v-btn>
                        <v-btn small class="primary" @click="save">Save</v-btn> -->
                        <AssetsButton
                          :options="{
                            label: `Cancel`,
                            icon: ``,
                            color: `grey white--text`,
                          }"
                          @click="userDialog = false"
                        />
                        <AssetsButton
                          :options="{
                            label: `Submit`,
                            icon: ``,
                            color: `blue`,
                          }"
                          @click="save"
                        />
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
    <v-container fluid>
      <v-toolbar class="rounded-md" dense flat>
        <v-spacer></v-spacer>

        <v-btn
          v-if="can('employee_create')"
          class="primary"
          dark
          small
          @click="addNewItem"
        >
          <v-icon small center>mdi-plus</v-icon> New
        </v-btn>
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
        <template v-slot:item.photo="{ item }">
          <v-avatar>
            <v-img :src="item.image || '/no-image.PNG'"> </v-img>
          </v-avatar>
        </template>

        <template v-slot:item.name="{ item }">
          {{ item.title }} {{ item.name }} {{ item.last_name }}
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
            v-if="can('employee_edit') || can('employee_delete')"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn dark-2 icon v-bind="attrs" v-on="on">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>

            <v-list width="120" dense>
              <v-list-item v-if="can('employee_edit')" @click="editItem(item)">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small> mdi-pencil </v-icon>
                  Edit
                </v-list-item-title>
              </v-list-item>
              <v-list-item
                v-if="can('employee_delete')"
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
    </v-container>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  props: ["user_type"],
  data() {
    return {
      Rules: [(v) => !!v || "This field is required"],
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
        { text: "Pin", value: "pin", key: "pin", filterable: true },
        { text: "Mobile", value: "mobile", key: "mobile", filterable: true },
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
        role_id: 0,
        title: "Mr",
        name: "",
        last_name: "",
        password: "",
        password_confirmation: "",
        email: "",
        mobile: "",
        is_active: 1,
        enable_whatsapp_otp: 0,
      },

      defaultItem: {
        role_id: 0,
        title: "Mr",
        name: "",
        last_name: "",
        password: "",
        password_confirmation: "",
        email: "",
        mobile: "",
        is_active: 1,
        enable_whatsapp_otp: 0,
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
    isUserTypeEmployee() {
      return this.user_type == "employee";
    },
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },
  created() {},

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

    addNewItem() {
      this.userDialog = true;
      this.errors = [];
      this.editedItem = {};
      this.editedIndex = -1;
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

    deleteItem(item) {
      confirm("Are you sure you wish to delete?") &&
        this.$axios
          .delete(`users/${item.id}`)
          .then(({ data }) => {
            this.getDataFromApi();
          })
          .catch((err) => console.log(err));
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
            user_type: this.user_type,
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
    save() {
      this.errors = [];
      let payload = new FormData();

      if (this.editedItem.title) {
        payload.append("title", this.editedItem.title || 0);
      }
      if (this.editedItem.name) {
        payload.append("name", this.editedItem.name || 0);
      }
      if (this.editedItem.last_name) {
        payload.append("last_name", this.editedItem.last_name || 0);
      }
      if (this.editedItem.mobile) {
        payload.append("mobile", this.editedItem.mobile);
      }
      if (this.editedItem.pin) {
        payload.append("pin", this.editedItem.pin);
      }
      if (this.editedItem.device_id) {
        payload.append("device_id", this.editedItem.device_id);
      }

      payload.append("is_active", this.editedItem.is_active);

      if (this.upload.name) {
        payload.append("image", this.upload.name);
      }

      payload.append("company_id", this.$auth.user.company.id);

      payload.append("user_type", this.user_type); //user_type employee,house_keeping,maintenance

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
              this.search = "";
            }
          })
          .catch((res) => console.log(res));
      }
    },
  },
};
</script>

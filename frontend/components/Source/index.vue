<template>
  <div v-if="can(`source_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-dialog v-model="agentDialog" max-width="40%">
      <v-card>
        <v-alert class="rounded-md" color="grey lighten-3" dense flat>
          <v-row no-gutter>
            <v-col>
              <span>{{ formTitle }} {{ Model }}</span>
            </v-col>
            <v-col class="text-right">
              <AssetsButtonClose @close="agentDialog = false" />
            </v-col>
          </v-row>
        </v-alert>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col md="6" cols="12">
                <v-text-field
                  v-model="editedItem.name"
                  placeholder="Company"
                  :hide-details="true"
                  outlined
                  label="Company"
                  dense
                ></v-text-field>
                <span v-if="errors && errors.name" class="error--text">{{
                  errors.name[0]
                }}</span>
              </v-col>
              <v-col md="6" cols="12">
                <v-text-field
                  v-model="editedItem.contact_name"
                  placeholder="Name"
                  label="Name"
                  outlined
                  :hide-details="true"
                  dense
                ></v-text-field>
                <span
                  v-if="errors && errors.contact_name"
                  class="error--text"
                  >{{ errors.contact_name[0] }}</span
                >
              </v-col>
              <!-- <v-col md="12" cols="12">
                  <v-select
                    v-model="editedItem.type"
                    :items="sourceTypeList"
                    placeholder="Type"
                    dense
                    label="Type"
                    item-text="name"
                    item-value="value"
                    outlined
                    :hide-details="true"
                  ></v-select>
                  <span v-if="errors && errors.type" class="error--text">{{
                    errors.type[0]
                  }}</span>
                </v-col> -->
              <v-col md="6" cols="6">
                <v-text-field
                  v-model="editedItem.mobile"
                  placeholder="Mobile"
                  label="Mobile"
                  outlined
                  dense
                  :hide-details="true"
                  type="number"
                ></v-text-field>
                <span v-if="errors && errors.mobile" class="error--text">{{
                  errors.mobile[0]
                }}</span>
              </v-col>
              <v-col md="6" cols="12">
                <v-text-field
                  v-model="editedItem.landline"
                  placeholder="Landline"
                  outlined
                  label="Landline"
                  dense
                  :hide-details="true"
                  type="number"
                ></v-text-field>
                <span v-if="errors && errors.mobile" class="error--text">{{
                  errors.mobile[0]
                }}</span>
              </v-col>
              <v-col md="6" cols="12">
                <v-text-field
                  v-model="editedItem.email"
                  placeholder="Email"
                  outlined
                  label="Email"
                  dense
                  :hide-details="true"
                  type="email"
                ></v-text-field>
                <span v-if="errors && errors.email" class="error--text">{{
                  errors.email[0]
                }}</span>
              </v-col>
              <v-col md="6" cols="12">
                <v-text-field
                  v-model="editedItem.gst"
                  placeholder="GST"
                  label="GST"
                  :hide-details="true"
                  outlined
                  dense
                ></v-text-field>
                <span v-if="errors && errors.gst" class="error--text">{{
                  errors.gst[0]
                }}</span>
              </v-col>
              <v-col md="12" cols="12">
                <v-text-field
                  v-model="editedItem.address"
                  placeholder="Address"
                  outlined
                  label="Address"
                  textarea
                  hide-details
                ></v-text-field>
                <span v-if="errors && errors.address" class="error--text">{{
                  errors.gst[0]
                }}</span>
              </v-col>
              <v-col cols="12" class="text-right">
                <AssetsButtonCancel @click="agentDialog = false" />
                &nbsp; &nbsp;
                <AssetsButtonSubmit @click="save" />
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-row class="px-2 pb-2">
      <v-col cols="2">
        <v-text-field
          class="global-search-textbox"
          append-icon="mdi-magnify"
          label="Search..."
          clearable
          dense
          outlined
          hide-details
          @input="getDataFromApi"
          v-model="search"
        ></v-text-field>
      </v-col>
      <v-col class="text-right">
        <v-btn
          v-if="can('source_create')"
          class="py-3"
          @click="agentDialog = true"
          x-small
          color="primary"
        >
          <v-icon color="white" small class="py-5">mdi-plus</v-icon>
          New
        </v-btn>
        <!-- <FilterDateRange height="30" @filter-attr="filterAttr" /> -->
      </v-col>
    </v-row>
    <v-data-table
      dense
      :headers="headers"
      :items="data"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [100, 500, 1000],
      }"
      class="elevation-1 px-2"
    >
      <template v-slot:item.name="{ item }">
        <small class="text-color">{{
          caps(item.contact_name)
        }}</small></template
      >
      <template v-slot:item.company="{ item }">
        <small class="text-color">{{ caps(item.name) }}</small></template
      >
      <template v-slot:item.type="{ item }">
        <small class="text-color">{{ caps(item.type) }}</small></template
      >
      <template v-slot:item.mobile="{ item }">
        <small class="text-color">{{ caps(item.mobile) }}</small></template
      >
      <template v-slot:item.landline="{ item }">
        <small class="text-color">{{ caps(item.landline) }}</small></template
      >
      <template v-slot:item.email="{ item }">
        <small class="text-color">{{ caps(item.email) }}</small></template
      >
      <template v-slot:item.gst="{ item }">
        <small class="text-color">{{ caps(item.gst) }}</small></template
      >
      <template v-slot:item.address="{ item }">
        <small class="text-color">{{ caps(item.address) }}</small></template
      >
      <template v-slot:item.created_at="{ item }">
        <small class="text-color">{{ caps(item.created_at) }}</small></template
      >
      <template v-slot:item.action="{ item }">
        <v-menu bottom left v-if="can('source_edit') || can('source_delete')">
          <template v-slot:activator="{ on, attrs }">
            <v-btn dark-2 icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list width="120" dense>
            <v-list-item v-if="can('source_edit')" @click="editItem(item)">
              <v-list-item-title style="cursor: pointer">
                <v-icon color="secondary" small> mdi-pencil </v-icon>
                Edit
              </v-list-item-title>
            </v-list-item>
            <v-list-item v-if="type == 'corporate'">
              <v-list-item-title style="cursor: pointer">
                <SourceGuestCreate />
              </v-list-item-title>
            </v-list-item>
            <!-- <v-list-item v-if="can('source_edit')" @click="editItem(item)">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small> mdi-eye-outline </v-icon>
                  View Guest
                </v-list-item-title>
              </v-list-item> -->
            <v-list-item v-if="can('source_delete')" @click="deleteItem(item)">
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
  props: ["type"],
  data: () => ({
    headers: [
      { text: "Name", value: "name" },
      { text: "Company", value: "company" },
      { text: "Type", value: "type" },
      { text: "Mobile", value: "mobile" },
      { text: "Landline", value: "landline" },
      { text: "Email", value: "email" },
      { text: "GST", value: "gst" },
      { text: "Address", value: "address" },
      { text: "created_at", value: "created_at" },
      { text: "Action", value: "action" },
    ],
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    Model: "Sources",
    options: {},
    endpoint: "source",
    search: "",
    snackbar: false,
    dialog: false,
    agentDialog: false,
    ids: [],
    loading: false,
    total: 0,

    editedIndex: -1,
    editedItem: {
      contact_name: "",
      address: "",
      gst: "",
      mobile: "",
      name: "",
      type: "",
      email: "",
      landline: "",
    },
    defaultItem: {
      contact_name: "",
      address: "",
      gst: "",
      mobile: "",
      name: "",
      type: "",
      email: "",
      landline: "",
    },
    response: "",
    data: [],
    sourceTypeList: [
      {
        name: "Online",
        value: "Online",
      },
      {
        name: "Travel Agent",
        value: "Agent",
      },
      {
        name: "Corporate",
        value: "corporate",
      },
    ],
    errors: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },

  watch: {
    agentDialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
  },

  created() {
    this.loading = true;
    this.getDataFromApi();
  },

  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    onPageChange() {
      this.getDataFromApi();
    },
    getDataFromApi() {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          page: page,
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          search: this.search,
          type: this.type,
        },
      };

      this.$axios.get(this.endpoint, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },
    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.agentDialog = true;
    },

    delteteSelectedRecords() {
      let just_ids = this.ids.map((e) => e.id);
      confirm(
        "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .post(`${this.endpoint}/delete/selected`, {
            ids: just_ids,
          })
          .then((res) => {
            if (!res.data.status) {
              this.errors = res.data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = res.data.status;
              this.ids = [];
              this.response = "Selected records has been deleted";
            }
          })
          .catch((err) => console.log(err));
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
          })
          .catch((err) => console.log(err));
    },

    close() {
      this.agentDialog = false;

      // setTimeout(() => {
      this.editedItem = Object.assign({}, this.defaultItem);
      this.editedIndex = -1;
      // }, 300);
    },

    save() {
      let payload = {
        name: this.editedItem.name.toLowerCase(),
        type: this.type,
        gst: this.editedItem.gst,
        address: this.editedItem.address,
        mobile: this.editedItem.mobile,
        email: this.editedItem.email,
        landline: this.editedItem.landline,
        contact_name: this.editedItem.contact_name,
        company_id: this.$auth.user.company.id,
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              const index = this.data.findIndex(
                (item) => item.id == this.editedItem.id
              );
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
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

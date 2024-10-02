<template>
  <div v-if="can('setting_access') && can('setting_access')">
    <div class="text-center ma-2">
      <v-snackbar
        v-model="snackbar"
        top="top"
        :color="snackbarColor"
        elevation="24"
      >
        {{ snackbarResponse }}
      </v-snackbar>
    </div>
    <v-dialog v-model="newItemDialog" max-width="40%">
      <v-card>
        <v-toolbar flat dense class="primary white--text">
          <span>Edit Details </span>
          <v-spacer></v-spacer>
          <v-icon @click="newItemDialog = false" dark> mdi-close </v-icon>
        </v-toolbar>
        <v-card-text>
          <v-container>
            <v-row v-if="rowid == 1">
              <v-col md="12" cols="12">
                <v-text-field
                  label="Whatsapp Instanace ID"
                  v-model="whatsapp_instance_id"
                  outlined
                  dense
                  small
                  :hide-details="true"
                  placeholder="Whatsapp Instanace ID"
                ></v-text-field>
                <span
                  dense
                  v-if="errors && errors.whatsapp_instance_id"
                  class="error--text"
                  >{{ errors.whatsapp_instance_id[0] }}</span
                >
              </v-col>
            </v-row>
            <v-row v-else>
              <v-col md="12" cols="12">
                <v-text-field
                  label="Whatsapp Token ID"
                  v-model="whatsapp_access_token"
                  outlined
                  dense
                  small
                  :hide-details="true"
                  placeholder="Whatsapp Token ID"
                ></v-text-field>
                <span
                  dense
                  v-if="errors && errors.whatsapp_access_token"
                  class="error--text"
                  >{{ errors.whatsapp_access_token[0] }}</span
                >
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" class="text-right" v-if="!viewMode">
                <v-btn dark filled color="grey white--text" small @click="newItemDialog = false"
                  >Cancel</v-btn
                >
                <v-btn small @click="save()" dark filled color="primary">Save</v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-card class="mb-5" elevation="0">
      <v-container fluid>
        <v-row>
          <v-col md="4" cols="12" class="pl-5">
            <label>Whatsapp Instance Id</label>
          </v-col>

          <v-col md="4" cols="12">
            <label>: {{ company_payload.whatsapp_instance_id }}</label>
          </v-col>
          <v-col md="4" cols="12">
            <span @click="editItem(1)" style="cursor: pointer">
              <v-icon color="secondary" small> mdi-pencil </v-icon>
              Edit
            </span>
          </v-col>
        </v-row>
        <v-row>
          <v-col md="4" cols="12" class="pl-5">
            <label>Whatsapp Access Token</label>
          </v-col>

          <v-col md="4" cols="12">
            <label>: {{ company_payload.whatsapp_access_token }}</label>
          </v-col>
          <v-col md="4" cols="12">
            <span @click="editItem(2)" style="cursor: pointer">
              <v-icon color="secondary" small> mdi-pencil </v-icon>
              Edit
            </span>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    whatsapp_instance_id: "",
    whatsapp_access_token: "",
    company_payload: {
      whatsapp_instance_id: "",
      whatsapp_access_token: "",
    },
    //datatable varables
    page: 1,
    perPage: 0,
    currentPage: 1,
    cumulativeIndex: 1,
    totalTableRowsCount: 0,
    options: {},
    filters: {},
    isFilter: false,
    data: [],
    loading: false,

    roomTypesData: [],

    endpoint: "tax_slabs",

    newItemDialog: false,

    //add edit item details
    editedItem: {},
    editedItemIndex: -1,
    roomTypesForSelectOptions: [],
    errors: {},
    snackbar: false,
    snackbarColor: "red",
    snackbarResponse: "",
    viewMode: false,
    rowid: 1,
  }),
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  created() {
    this.company_payload.whatsapp_instance_id =
      this.$auth.user.company.whatsapp_instance_id;
    this.company_payload.whatsapp_access_token =
      this.$auth.user.company.whatsapp_access_token;

    //this.getDataFromApi();
  },
  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    editItem(id) {
      this.whatsapp_instance_id = this.$auth.user.company.whatsapp_instance_id;
      this.whatsapp_access_token =
        this.$auth.user.company.whatsapp_access_token;
      this.errors = {};
      this.newItemDialog = true;
      this.rowid = id;
    },
    save() {
      let payload = new FormData();

      payload.append("whatsapp_instance_id", this.whatsapp_instance_id);
      payload.append("whatsapp_access_token", this.whatsapp_access_token);

      let company_id = this.$auth?.user?.company?.id;
      this.$axios
        .post(`/company/${company_id}/update_settings`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.company_payload.whatsapp_instance_id =
              this.whatsapp_instance_id;
            this.company_payload.whatsapp_access_token =
              this.whatsapp_access_token;
            this.snackbarColor = "primary";
            this.snackbar = true;
            this.snackbarResponse = " updated successfully";
            this.newItemDialog = false;
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>

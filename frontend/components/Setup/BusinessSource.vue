<template>
  <div
    v-if="
      can(`settings_rooms_category_access`) &&
      can(`settings_rooms_category_view`)
    "
  >
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-dialog v-model="roomTypeDialog" max-width="400">
      <v-card>
        <v-toolbar color="primary" dense flat dark>
          <span>{{ formTitle }} {{ Model }}</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="roomTypeDialog = false"
            >mdi-close</v-icon
          >
        </v-toolbar>
        <v-container>
          <v-row>
            <v-col md="12" lg="12">
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
            <v-col cols="12" class="text-right">
              <v-btn
                small
                class="grey white--text"
                @click="roomTypeDialog = false"
              >
                Cancel
              </v-btn>
              <v-btn small class="primary" @click="save">Save</v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </v-dialog>

    <v-card elevation="0">
      <v-card-text>
        <v-container fluid>
          <v-row>
            <v-col cols="3">
              <div class="d-flex align-center">
                <div>{{ Model }}</div>

                <v-icon color="primary" class="ml-2">mdi-reload</v-icon>

                <!-- <v-text-field
                  label="Search..."
                  dense
                  outlined
                  flat
                  append-icon="mdi-magnify"
                  @input="searchIt"
                  v-model="search"
                  hide-details
                ></v-text-field> -->
              </div>
            </v-col>
            <v-col class="text-right">
              <v-btn
                dark
                class="blue"
                v-if="can(`settings_rooms_category_create`)"
                small
                @click="
                  roomTypeDialog = true;
                  previewImage = null;
                "
              >
                <v-icon color="white" small>mdi-plus</v-icon>
                Business Source
              </v-btn>
            </v-col>
            <v-col cols="12">
              <v-data-table
                :headers="headersBusinessSource"
                :items="data"
                :loading="loading"
                hide-default-footer
                elevation="0"
              >
                <template v-slot:item.tax="{ item }">
                  {{ item.tax }}%
                </template>
                <template v-slot:item.action="{ item }">
                  <v-menu
                    bottom
                    left
                    v-if="
                      can('settings_rooms_category_edit') ||
                      can('settings_rooms_category_delete')
                    "
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn dark-2 icon v-bind="attrs" v-on="on">
                        <v-icon>mdi-dots-vertical</v-icon>
                      </v-btn>
                    </template>
                    <v-list width="120" dense>
                      <v-list-item
                        v-if="can('settings_rooms_category_edit')"
                        @click="editItem(item)"
                      >
                        <v-list-item-title style="cursor: pointer">
                          <v-icon color="secondary" small>mdi-pencil</v-icon>
                          Edit
                        </v-list-item-title>
                      </v-list-item>
                      <v-list-item
                        v-if="can('settings_rooms_category_delete')"
                        @click="deleteItem(item)"
                      >
                        <v-list-item-title style="cursor: pointer">
                          <v-icon color="error" small>mdi-delete</v-icon>
                          Delete
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </template>
              </v-data-table>
            </v-col>
            <v-col cols="12" class="float-right">
              <div class="float-right">
                <v-pagination
                  v-model="pagination.current"
                  :length="pagination.total"
                  @input="onPageChange"
                  :total-visible="12"
                ></v-pagination>
              </div>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    previewImage: null,
    componentKey: 0,
    activeTab: 0,

    pagination: {
      current: 1,
      total: 0,
      per_page: 50,
    },
    Model: "Business Source",
    options: {},
    endpoint: "business-source",
    search: "",
    snackbar: false,
    dialog: false,
    roomTypeDialog: false,
    ids: [],
    loading: false,
    total: 0,

    editedIndex: -1,
    editedItem: {
      name: "",
      slug: "",
      company_id: 0,
    },

    defaultItem: {
      name: "",
      slug: "",
      company_id: 0,
    },

    response: "",
    data: [],
    headersBusinessSource: [
      { text: "Name", value: "name" },
      { text: "Action", value: "action", align: "center" },
    ],
    selectedFile: "",
    errors: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },

  watch: {
    roomTypeDialog(val) {
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
    onPageChange() {
      this.getDataFromApi();
    },
    getDataFromApi() {
      this.loading = true;
      let options = {
        params: {
          page: this.pagination.current,
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          search: this.search,
        },
      };

      this.$axios.get(this.endpoint, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },
    searchIt(e) {
      this.getDataFromApi();
    },

    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);

      this.previewImage = item.pic;
      this.roomTypeDialog = true;
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(`room_types/` + item.id)
          .then(({ data }) => {
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
          })
          .catch((err) => console.log(err));
    },

    close() {
      this.roomTypeDialog = false;
      // setTimeout(() => {
      this.editedItem = Object.assign({}, this.defaultItem);
      this.editedIndex = -1;
      // }, 300);
    },

    save() {
      this.editedItem.slug = this.editedItem.name;
      this.editedItem.company_id = this.$auth.user.company_id;

      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, this.editedItem)
          .then(({ data }) => {
            this.getDataFromApi();
            this.snackbar = data.status;
            this.response = data.message;
            this.close();
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, this.editedItem)
          .then(({ data }) => {
            this.getDataFromApi();
            this.snackbar = data.status;
            this.response = data.message;
            this.close();
            this.errors = [];
            this.search = "";
          })
          .catch((res) => console.log(res));
      }
    },
  },
};
</script>

<style scoped>
@import url("../../assets/css/tableStyles.css");
</style>

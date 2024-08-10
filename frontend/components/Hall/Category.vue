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
    <v-dialog v-model="roomTypeDialog" max-width="50%">
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
              <v-card elevation="0">
                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col md="8">
                        <v-row>
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
                            <v-select
                              v-model="editedItem.tax"
                              :items="[
                                { value: 0, name: `Exempted %` },
                                { value: 5, name: `5%` },
                                { value: 12, name: `12%` },
                                { value: 18, name: `18%` },
                                { value: 28, name: `28%` },
                              ]"
                              placeholder="GST"
                              dense
                              label="GST"
                              item-text="name"
                              item-value="value"
                              outlined
                              :hide-details="true"
                            ></v-select>
                            <span
                              v-if="errors && errors.adult"
                              class="error--text"
                              >{{ errors.adult[0] }}</span
                            >
                          </v-col>
                          <v-col md="12" cols="12">
                            <v-text-field
                              v-model="editedItem.short_description"
                              placeholder="Short Description"
                              label="Short Description"
                              outlined
                              :hide-details="true"
                              dense
                            ></v-text-field>
                          </v-col>
                          <v-col md="12" cols="12">
                            <v-textarea
                              rows="3"
                              outlined
                              v-model="editedItem.description"
                              placeholder="Description"
                              label="Long Description"
                            ></v-textarea>
                          </v-col>
                        </v-row>
                      </v-col>
                      <v-col cols="4">
                        <div
                          class="form-group"
                          style="margin: 0 auto; width: 200px"
                        >
                          <v-img
                            style="
                              width: 100%;
                              height: 200px;
                              border: 1px solid #4390fc;
                              border-radius: 10%;
                              margin: 0 auto;
                            "
                            :src="previewImage || '/noimage.png'"
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
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>

    <v-card elevation="0">
      <v-card-text>
        <v-container fluid>
          <v-row>
            <v-col cols="3">
              <v-text-field
                class=""
                label="Search..."
                dense
                outlined
                flat
                append-icon="mdi-magnify"
                @input="searchIt"
                v-model="search"
                hide-details
              ></v-text-field>
            </v-col>
            <v-col cols="9" class="text-right">
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
                Hall
              </v-btn>
            </v-col>
            <v-col cols="12">
              <v-data-table
                :headers="headersCategory"
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
// import roomsComponent from '../../components/roomsComponent.vue';
export default {
  // components: {
  //   roomsComponent,
  // },
  data: () => ({
    upload: {
      name: "",
    },
    previewImage: null,
    componentKey: 0,
    activeTab: 0,

    pagination: {
      current: 1,
      total: 0,
      per_page: 50,
    },
    Model: "Hall",
    options: {},
    endpoint: "get_room_type_list",
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
      adult: 0,
      child: 0,
      baby: 0,
      tax: 0,
      price: 0,
      short_description: "",
      description: "",
    },

    defaultItem: {
      name: "",
      adult: "",
      child: "",
      baby: "",
      tax: 0,
      price: "",
      short_description: "",
      description: "",
    },

    response: "",
    data: [],
    headersCategory: [
      { text: "#", value: "id", align: "start" },
      { text: "Name", value: "name" },
      { text: "tax", value: "tax" },
      { text: "short_description", value: "short_description" },
      { text: "description", value: "description" },
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
    updatePriceToWordpress() {
      //console.log(this.$auth.user.company);
      window.open(
        this.$auth.user.company.wordpress_push_prices_url,
        "blank",
        "noreferrer"
      );
      // //update price to wordpress
      this.$axios
        .get(this.$auth.user.company.wordpress_push_prices_url, null)
        .then(({ data }) => {
          console.log(data);
        })
        .catch((res) => console.log(res));
    },
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
          //this.$refs.cropper.replace(this.selectedFile);
        };
        reader.readAsDataURL(file[0]);
        this.$emit("input", file[0]);

        this.dialogCropping = true;
      }
    },
    getDataFromApi() {
      this.loading = true;
      let options = {
        params: {
          page: this.pagination.current,
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          search: this.search,
          type: "hall",
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
      // let payload1 = {
      //   name: this.editedItem.name,
      //   price: this.editedItem.price,
      //   adult: this.editedItem.adult,
      //   child: this.editedItem.child,
      //   baby: this.editedItem.baby,
      //   company_id: this.$auth.user.company.id,
      //   short_description: this.editedItem.short_description,
      //   description: this.editedItem.description,

      // };

      let payload = new FormData();

      if (this.upload.name) {
        payload.append("image", this.upload.name);
      }
      payload.append("name", this.editedItem.name);
      payload.append("price", 0);
      payload.append("adult", 0);
      payload.append("child", 0);
      payload.append("tax", parseFloat(this.editedItem.tax));
      payload.append("baby", 0);
      payload.append("company_id", this.$auth.user.company.id);
      payload.append("short_description", this.editedItem.short_description);
      payload.append("description", this.editedItem.description);
      payload.append("type", "hall");

      // if (this.upload.name) {
      //   payload.append("image", this.upload.name);
      // }
      if (this.editedIndex > -1) {
        payload.append("_method", "PUT");
        this.$axios
          .post("room_types" + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            console.log(data);
            if (!data.status) {
              this.errors = data.errors;
            } else {
              console.log("suc");
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post("room_types", payload)
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

<style scoped>
@import url("../../assets/css/tableStyles.css");
</style>

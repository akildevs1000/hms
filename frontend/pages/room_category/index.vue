<template>
  <div v-if="can(`settings_rooms_category_access`) && can(`settings_rooms_category_view`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-0 mb-0">
      <v-col cols="6">
        <h3>{{ Model }}</h3>

      </v-col>
    </v-row>
    <div>
      <v-dialog v-model="roomTypeDialog" max-width="40%">
        <v-card>
          <v-toolbar color="background" dense flat dark>
            <v-toolbar color="background" dense flat dark>
              <span>{{ formTitle }} {{ Model }}</span>
            </v-toolbar>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="roomTypeDialog = false">mdi mdi-close-box</v-icon>
          </v-toolbar>
          <v-container>
            <v-row>
              <v-col md="12" lg="12">
                <v-card elevation="0">
                  <v-card-text>
                    <v-container>
                      <v-row>
                        <v-col md="6" cols="12">
                          <v-text-field v-model="editedItem.name" placeholder="Name" label="Name" outlined
                            :hide-details="true" dense></v-text-field>
                          <span v-if="errors && errors.name" class="error--text">{{ errors.name[0]
                          }}</span>
                        </v-col>
                        <v-col md="6" cols="12">
                          <v-text-field v-model="editedItem.price" placeholder="Amount" label="Amount" outlined
                            :hide-details="true" type="number" dense></v-text-field>
                          <span v-if="errors && errors.price" class="error--text">{{ errors.price[0]
                          }}</span>
                        </v-col>
                        <v-col md="4" cols="12">
                          <v-select v-model="editedItem.adult" :items="[1, 2, 3, 4]" placeholder="Adult" dense
                            label="Adult" item-text="name" item-value="value" outlined :hide-details="true"></v-select>
                          <span v-if="errors && errors.adult" class="error--text">{{ errors.adult[0] }}</span>
                        </v-col>

                        <v-col md="4" cols="12">
                          <v-select v-model="editedItem.child" :items="[1, 2, 3, 4]" placeholder="Child" dense
                            label="Child" item-text="name" item-value="value" outlined :hide-details="true"></v-select>
                          <span v-if="errors && errors.child" class="error--text">{{ errors.child[0] }}</span>
                        </v-col>

                        <v-col md="4" cols="12">
                          <v-select v-model="editedItem.baby" :items="[1, 2, 3]" placeholder="Baby" dense label="Baby"
                            item-text="name" item-value="value" outlined :hide-details="true"></v-select>
                          <span v-if="errors && errors.baby" class="error--text">{{ errors.baby[0] }}</span>
                        </v-col>


                        <v-card-actions>
                          <v-btn class="error" @click="roomTypeDialog = false">
                            Cancel
                          </v-btn>
                          <v-btn class="primary" @click="save">Save</v-btn>
                        </v-card-actions>

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

      <div>
        <v-row>

          <v-col md="12" lg="12">
            <v-tabs v-model="activeTab" :vertical="vertical" background-color="background" dense flat dark show-arrows
              class=" rounded-t-sm">

              <v-spacer></v-spacer>
              <v-tab active-class="active-link">
                Categories
              </v-tab>
              <v-tab active-class="active-link">
                Rooms
              </v-tab>
              <v-tabs-slider color="#1259a7"></v-tabs-slider>
              <v-tab-item>

                <v-col md="12" lg="12" class="pt-0">
                  <v-col xs="12" sm="12" md="2" cols="12">
                    <v-text-field class="" label="Search..." dense outlined flat append-icon="mdi-magnify"
                      @input="searchIt" v-model="search" hide-details></v-text-field>
                  </v-col>
                  <v-card>
                    <v-toolbar class=" mb-2 white--text" color="background" dense flat>

                      <v-spacer></v-spacer>
                      <v-tooltip v-if="can('settings_rooms_create')" top color="background">
                        <template v-slot:activator="{ on, attrs }">


                          <v-btn v-if="can(`settings_rooms_category_create`)" x-small :ripple="false" text v-bind="attrs"
                            v-on="on" @click="roomTypeDialog = true">
                            <v-icon color="white" dark white>mdi-plus-circle</v-icon>
                          </v-btn>
                        </template>

                      </v-tooltip>

                    </v-toolbar>
                    <v-row>
                      <v-col cols="12">
                        <table class="mt-0 ">
                          <tr style="font-size: 13px">
                            <th class="ps-5">#</th>
                            <th>Name</th>
                            <th>Adult</th>
                            <th>Child</th>
                            <th>Baby</th>
                            <th>Weekday Rent</th>
                            <th>Weekend Rent</th>
                            <th>Holiday Rent</th>
                            <th class="text-center">Action</th>
                          </tr>
                          <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
                            color="primary"></v-progress-linear>
                          <tr v-for="(            item, index            ) in             data            " :key="index"
                            style="font-size: 13px">
                            <td>
                              {{
                                (pagination.current - 1) * pagination.per_page + index + 1
                              }}
                            </td>
                            <td>{{ caps(item.name) }}</td>
                            <td>{{ caps(item.adult) }}</td>
                            <td>{{ caps(item.child) }}</td>
                            <td>{{ caps(item.baby) }}</td>
                            <td>{{ caps(item.weekday_price) }}</td>
                            <td>{{ caps(item.weekend_price) }}</td>
                            <td>{{ caps(item.holiday_price) }}</td>

                            <td class="text-center">
                              <v-menu bottom left v-if="can(`settings_rooms_category_edit`) || can(`settings_rooms_category_delete`)
                                ">
                                <template v-slot:activator="{ on, attrs }">
                                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                                    <v-icon>mdi-dots-vertical</v-icon>
                                  </v-btn>
                                </template>
                                <v-list width="120" dense>
                                  <v-list-item v-if="can(`settings_rooms_category_edit`)
                                    " @click="editItem(item)">
                                    <v-list-item-title style="cursor: pointer">
                                      <v-icon color="secondary" small>
                                        mdi-pencil
                                      </v-icon>
                                      Edit
                                    </v-list-item-title>
                                  </v-list-item>
                                  <v-list-item v-if="can(`settings_rooms_category_delete`)
                                    " @click="deleteItem(item)">
                                    <v-list-item-title style="cursor: pointer">
                                      <v-icon color="error" small> mdi-delete </v-icon>
                                      Delete
                                    </v-list-item-title>
                                  </v-list-item>
                                </v-list>
                              </v-menu>
                            </td>
                          </tr>
                        </table>
                      </v-col>
                    </v-row>
                  </v-card>
                </v-col>
                <v-col md="12" class="float-right">
                  <div class="float-right">
                    <v-pagination v-model="pagination.current" :length="pagination.total" @input="onPageChange"
                      :total-visible="12"></v-pagination>
                  </div>
                </v-col>

              </v-tab-item>
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <client-only>
                      <roomsComponent :key="componentKey" />
                    </client-only>
                  </v-card-text>
                </v-card>
              </v-tab-item>
            </v-tabs>
          </v-col>

        </v-row>
      </div>
    </div>
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
    componentKey: 0,
    vertical: false,
    activeTab: 0,

    pagination: {
      current: 1,
      total: 0,
      per_page: 50,
    },
    Model: "Room Category",
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
      adult: "",
      child: "",
      baby: "",
      price: "",
    },

    defaultItem: {
      name: "",
      adult: "",
      child: "",
      baby: "",
      price: "",
    },

    response: "",
    data: [],

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
    getDataFromApi(url = this.endpoint) {

      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          search: this.search,
        },
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },
    searchIt(e) {
      let s = e.toLowerCase();
      this.getDataFromApi();
      return;
    },

    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);


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
      this.roomTypeDialog = false;

      // setTimeout(() => {
      this.editedItem = Object.assign({}, this.defaultItem);
      this.editedIndex = -1;
      // }, 300);
    },

    save() {
      let payload = {
        name: this.editedItem.name,
        price: this.editedItem.price,
        adult: this.editedItem.adult,
        child: this.editedItem.child,
        baby: this.editedItem.baby,
        company_id: this.$auth.user.company.id,
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put('room_types' + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            console.log(data);
            if (!data.status) {
              this.errors = data.errors;
            } else {
              console.log('suc');
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post('room_types', payload)
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
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 5px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}

input[type="text"]:focus.custom-text-box {
  border: 2px solid #5fafa3 !important;
}
</style>

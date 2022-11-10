<template>
  <div v-if="can(`announcement_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>

      <v-col cols="6">
        <div class="text-right">
          <v-btn
            v-if="can(`announcement_delete_all`)"
            small
            color="error"
            class="mr-2 mb-2"
            @click="delteteSelectedRecords"
            >Delete Selected Records
          </v-btn>
          <v-btn
            v-if="can(`announcement_create`)"
            small
            color="primary"
            @click="dialog = true"
            class="mb-2"
            >{{ Model }} +</v-btn
          >
        </div>
      </v-col>
    </v-row>

    <v-dialog v-model="dialog" max-width="60%">
      <v-card>
        <v-card-title>
          <span class="headline">{{ formTitle }} {{ Model }}</span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="editedItem.title"
                  label="Title"
                  :error-messages="
                    errors && errors.title ? errors.title[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <!-- {{ employees_dialog }} -->

              <v-col cols="12">
                <v-autocomplete
                  v-model="editedItem.department"
                  :items="departments"
                  @change="employeesByDepartment"
                  item-text="name"
                  item-value="id"
                  label="Departments"
                  multiple
                  chips
                  :error-messages="
                    errors && errors.departments ? errors.departments[0] : ''
                  "
                ></v-autocomplete>
              </v-col>
              <v-col cols="12">
                <v-autocomplete
                  v-model="editedItem.employee"
                  :items="employees_dialog"
                  item-text="name_with_user_id"
                  item-value="id"
                  label="Employee"
                  multiple
                  chips
                  :error-messages="
                    errors && errors.employee ? errors.employee[0] : ''
                  "
                ></v-autocomplete>
              </v-col>

              <v-col cols="6">
                <v-menu
                  ref="from_menu"
                  v-model="start_menu"
                  :close-on-content-click="false"
                  :return-value.sync="editedItem.start_date"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <div class="mb-1">Start Date</div>
                    <v-text-field
                      dense
                      v-model="editedItem.start_date"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    >
                    </v-text-field>
                  </template>
                  <v-date-picker
                    v-model="editedItem.start_date"
                    no-title
                    scrollable
                  >
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="from_menu = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="$refs.from_menu.save(editedItem.start_date)"
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu>
                <span
                  v-if="errors && errors.start_date"
                  class="text-danger mt-2"
                  >{{ errors.start_date[0] }}</span
                >
              </v-col>
              <v-col cols="6">
                <v-menu
                  ref="end_menu"
                  v-model="end_menu"
                  :close-on-content-click="false"
                  :return-value.sync="editedItem.end_date"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <div class="mb-1">End Date</div>
                    <v-text-field
                      :hide-details="!editedItem.end_date"
                      dense
                      v-model="editedItem.end_date"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="editedItem.end_date"
                    no-title
                    scrollable
                  >
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="end_menu = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="$refs.end_menu.save(editedItem.end_date)"
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu>
                <span
                  v-if="errors && errors.end_date"
                  class="text-danger mt-2"
                  >{{ errors.end_date[0] }}</span
                >
              </v-col>
              <v-col cols="12">
                <ClientOnly>
                  <tiptap-vuetify
                    v-model="editedItem.description"
                    :extensions="extensions"
                    v-scroll.self="onScroll"
                    max-height="300"
                    :toolbar-attributes="{
                      color: 'primary lighten-2 red--text text--lighten-1'
                    }"
                  />
                  <template #placeholder> Loading... </template>
                </ClientOnly>
              </v-col>
              <span
                v-if="errors && errors.description"
                class="text-danger mt-2"
                >{{ errors.description[0] }}</span
              >
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="error" small @click="close"> Cancel </v-btn>
          <v-btn class="primary" small @click="save">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="view_notification" max-width="60%">
      <v-card>
        <v-toolbar class="pb-0 primary" dark flat>
          <div class="w-50">
            <span class="pb-0"><small>Announcement Details</small></span>
          </div>
          <div class="w-50 text-right">
            <span class="pb-0"
              ><small>Created: {{ desDate }}</small></span
            >
          </div>
        </v-toolbar>
        <v-card-text>
          <v-container>
            <h3>{{ title }}</h3>
            <v-divider></v-divider>
            <div>
              <span v-for="(item, index) in dept" :key="index">
                <v-chip small class="p-2 mx-1" color="primary">{{
                  item.name
                }}</v-chip>
              </span>
            </div>
            <v-divider></v-divider>

            <div v-html="des"></div>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-row>
      <v-col md="12">
        <v-data-table
          v-if="can(`announcement_view`)"
          v-model="ids"
          show-select
          item-key="id"
          :headers="headers"
          :items="data"
          :server-items-length="total"
          :loading="loading"
          :options.sync="options"
          :footer-props="{
            itemsPerPageOptions: [50, 100, 500, 1000]
          }"
          class="elevation-1"
        >
          <template v-slot:top>
            <v-toolbar class="rounded-md" color="background" dense flat dark>
              <span> {{ Model }} List</span>
            </v-toolbar>
            <v-toolbar flat color="">
              <v-toolbar-title>List</v-toolbar-title>
              <v-divider class="mx-4" inset vertical></v-divider>

              <v-text-field
                @input="searchIt"
                v-model="search"
                label="Search"
                single-line
                hide-details
              ></v-text-field>
            </v-toolbar>
          </template>
          <template v-slot:item.action="{ item }">
            <v-icon
              v-if="can(`announcement_edit`)"
              color="secondary"
              small
              class="mr-2"
              @click="editItem(item)"
            >
              mdi-pencil
            </v-icon>
            <v-icon
              v-if="can(`announcement_view`)"
              color="secondary"
              small
              class="mr-2"
              @click="viewItem(item)"
            >
              mdi-eye
            </v-icon>
            <v-icon
              v-if="can(`announcement_delete`)"
              color="error"
              small
              @click="deleteItem(item)"
            >
              {{ item.announcement === "customer" ? "" : "mdi-delete" }}
            </v-icon>
          </template>
          <template v-slot:item.departments="{ item }">
            <span v-for="(dep, index) in item.departments" :key="index">
              <v-chip small class="p-2 mx-1" color="primary">
                {{ dep.name }}
              </v-chip>
            </span>
          </template>

          <template v-slot:no-data>
            <!-- <v-btn color="primary" @click="initialize">Reset</v-btn> -->
          </template>
        </v-data-table>
      </v-col>
    </v-row>
  </div>

  <NoAccess v-else />
</template>
<script>
import {
  TiptapVuetify,
  Image,
  Heading,
  Bold,
  Italic,
  Strike,
  Underline,
  Code,
  Paragraph,
  BulletList,
  OrderedList,
  ListItem,
  Link,
  Blockquote,
  HardBreak,
  HorizontalRule,
  History
} from "tiptap-vuetify";

export default {
  components: {
    TiptapVuetify
  },
  data: () => ({
    //editor
    extensions: [
      History,
      Blockquote,
      Link,
      Image,
      Underline,
      Strike,
      Italic,
      ListItem,
      BulletList,
      OrderedList,
      [
        Heading,
        {
          options: {
            levels: [1, 2, 3]
          }
        }
      ],
      Bold,
      Link,
      Code,
      HorizontalRule,
      Paragraph,
      HardBreak
    ],
    // starting editor's content
    content: `
      <h1>Yay Headlines!</h1>
      <p>All these <strong>cool tags</strong> are working now.</p>
        `,

    //end editor
    scrollInvoked: 0,
    start_menu: false,
    end_menu: false,
    title: "",
    des: "",
    desDate: "",
    dept: "",
    options: {},
    Model: "Announcement",
    endpoint: "announcement",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    departments: [],

    loading: false,
    total: 0,
    headers: [
      { text: "Title", align: "left", sortable: false, value: "title" },
      {
        text: "Departments",
        align: "left",
        sortable: false,
        value: "departments"
      },
      { text: "Actions", align: "center", value: "action", sortable: false }
    ],
    editedIndex: -1,
    editedItem: {
      title: "",
      description: "",
      department: "",
      employee: "",
      start_date: null,
      end_date: null
    },
    defaultItem: {
      title: "",
      description: "",
      department: "",
      start_date: null,
      end_date: null
    },
    view_notification: false,
    response: "",
    data: [],
    errors: [],
    options_dialog: {},
    employees_dialog: []
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    }
  },

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true
    }
  },
  created() {
    this.loading = true;
    this.getDepartments();
    this.getEmployees();
  },

  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },
    onScroll() {
      this.scrollInvoked++;
    },

    getDepartments() {
      let options = {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`departments`, options).then(({ data }) => {
        this.departments = data.data;
      });
    },

    employeesByDepartment() {
      this.loading_dialog = true;
      const { page, itemsPerPage } = this.options_dialog;

      let options = {
        params: {
          department_ids: this.editedItem.department,
          per_page: itemsPerPage,
          page: page,
          company_id: this.$auth.user.company.id
        }
      };

      if (!this.editedItem.department.length) {
        this.getEmployees();
        return;
      }

      this.$axios.get("employeesByDepartment", options).then(({ data }) => {
        this.employees_dialog = data.data;
        this.employees_dialog.unshift({
          id: "---",
          name_with_user_id: "Select All"
        });
        this.loading_dialog = false;
      });
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id
        }
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.total = data.total;
        this.loading = false;
      });
    },
    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },

    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
      this.editedItem.department = item.departments.map(e => e.id);
    },

    viewItem(item) {
      this.view_notification = true;
      this.des = item.description;
      this.title = item.title;
      this.desDate = item.created_at;
      this.dept = item.departments;
    },

    delteteSelectedRecords() {
      let just_ids = this.ids.map(e => e.id);
      confirm(
        "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .post(`${this.endpoint}/delete/selected`, {
            ids: just_ids
          })
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.snackbar = data.status;
              this.ids = [];
              this.response = "Selected records has been deleted";
            }
            this.getDataFromApi();
          })
          .catch(err => console.log(err));
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
            }
          })
          .catch(err => console.log(err));
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    getEmployees(url = "employee") {
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id
        }
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.employees_dialog = data.data;
        this.employees_dialog.unshift({
          id: "---",
          name_with_user_id: "Select All"
        });
      });
    },

    save() {
      let editedItem = {
        title: this.editedItem.title.toLowerCase(),
        description: this.editedItem.description.toLowerCase(),
        start_date: this.editedItem.start_date,
        end_date: this.editedItem.end_date,
        departments:
          this.editedItem.department == "" ? [] : this.editedItem.department,
        employee:
          this.editedItem.employee == "" ? [] : this.editedItem.employee,
        company_id: this.$auth.user.company.id
      };

      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, editedItem)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              const index = this.data.findIndex(
                item => item.id == this.editedItem.id
              );
              this.data.splice(index, 1, {
                id: this.editedItem.id,
                name: this.editedItem.name
              });
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch(err => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, editedItem)
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
          .catch(res => console.log(res));
      }
    }
  }
};
</script>

<style>
.tiptap-vuetify-editor__content {
  min-height: 300px !important;
}

.ProseMirror .ProseMirror-focused {
  height: 400px !important;
}
</style>

<template>
  <div v-if="can(`leave_notification_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Employee / {{ Model }}</div>
      </v-col>

      <v-col cols="6">
        <div class="text-right">
          <!-- <v-btn
            v-if="can(`leave_notification_delete`)"
            small
            color="error"
            class="mr-2 mb-2"
            @click="delteteSelectedRecords"
            >Delete Selected Records
          </v-btn> -->
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

              <v-col cols="12">
                <v-select
                  v-model="editedItem.type"
                  :items="['annual', 'sick', 'parental', 'other']"
                  label="Type"
                  @change="changeType(editedItem.type)"
                  :error-messages="errors && errors.type ? errors.type[0] : ''"
                >
                </v-select>
              </v-col>

              <v-col cols="12" v-if="type_manual">
                <v-text-field
                  v-model="editedItem.type_manual"
                  label="Other"
                  :error-messages="
                    errors && errors.type_manual ? errors.type_manual[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                {{ editedItem.supervisor }}
                <v-autocomplete
                  v-model="editedItem.supervisor"
                  :items="reporters"
                  label="Supervisor"
                  item-text="first_name"
                  item-value="id"
                  chips
                  multiple
                  :error-messages="
                    errors && errors.supervisor ? errors.supervisor[0] : ''
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
                      Reject
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
                      Reject
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
          <v-btn class="error" small @click="close"> Close </v-btn>
          <v-btn class="primary" small @click="save">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="view_notification" max-width="60%">
      <v-card>
        <v-toolbar class="pb-0 primary" dark flat>
          <div class="w-50">
            <span class="pb-0"
              ><small>{{ Model }} Details</small></span
            >
          </div>
          <div class="w-50 text-right">
            <span class="pb-0" v-if="desDate"
              ><small>Created: {{ desDate }}</small></span
            >
          </div>
        </v-toolbar>
        <v-card-text>
          <v-container>
            <h3>{{ title }}</h3>
            <v-divider></v-divider>
            <div>
              <span v-if="approved_by">
                <strong> {{ approved_status }}:</strong>
                {{ approved_by || "" }}
              </span>
            </div>
            <div>
              <v-divider></v-divider>
              <v-chip
                v-if="leave_id && dept"
                class="primary pa-4"
                small
                color="primary"
              >
                {{ dept }}
              </v-chip>
            </div>
            <v-divider></v-divider>

            <div v-html="des"></div>
          </v-container>
          <div v-if="leave_id">
            <v-btn class="primary" small @click="status(1)">Approved</v-btn>
            <v-btn class="error" small @click="status(2)">Reject</v-btn>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-row>
      <v-col md="12">
        <v-data-table
          v-if="can(`leave_notification_list_view`)"
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
            <!-- <v-icon
              v-if="can(`announcement_edit`)"
              color="secondary"
              small
              class="mr-2"
              @click="editItem(item)"
            >
              mdi-pencil
            </v-icon> -->
            <v-icon
              v-if="can(`leave_notification_show`)"
              color="secondary"
              small
              class="mr-2"
              @click="viewItem(item)"
            >
              mdi-eye
            </v-icon>
            <!-- <v-icon
              v-if="can(`announcement_delete`)"
              color="error"
              small
              @click="deleteItem(item)"
            >
              mdi-delete
            </v-icon> -->
          </template>
          <!-- <template v-slot:item.departments="{ item }">
            <span v-for="(dep, index) in item.departments" :key="index">
              <v-chip small class="p-2 mx-1" color="primary">
                {{ dep.name }}
              </v-chip>
            </span>
          </template> -->

          <template v-slot:no-data> </template>

          <template v-slot:item.is_approved="{ item }">
            <label for="">
              <span>
                <v-chip
                  v-if="item.is_approved == 0"
                  small
                  class="p-2 mx-1"
                  color="primary"
                >
                  Pending
                </v-chip>

                <v-chip
                  v-else-if="item.is_approved == 1"
                  small
                  class="p-2 mx-1"
                  color="success"
                >
                  Approved
                </v-chip>

                <v-chip
                  v-if="item.is_approved == 2"
                  small
                  class="p-2 mx-1"
                  color="error"
                >
                  Reject
                </v-chip>
              </span>
            </label>
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
  layout: "employee",
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

    //end editor
    scrollInvoked: 0,
    start_menu: false,
    end_menu: false,
    title: "",
    des: "",
    desDate: "",
    view_notification: false,
    approved_by: "",
    approved_status: "",
    dept: "",
    options: {},
    Model: "Leave Request Notification",
    endpoint: "leave-notification",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    departments: [],

    type_manual: false,
    loading: false,
    total: 0,
    leave_id: 0,
    headers: [
      {
        text: "Employee Name",
        align: "left",
        sortable: false,
        value: "employee.name_with_user_id"
      },

      { text: "Title", align: "left", sortable: false, value: "title" },
      {
        text: "From",
        align: "left",
        sortable: false,
        value: "start_date"
      },
      {
        text: "To",
        align: "left",
        sortable: false,
        value: "end_date"
      },
      {
        text: "Status",
        align: "left",
        sortable: false,
        value: "is_approved"
      },
      { text: "Actions", align: "center", value: "action", sortable: false }
    ],
    editedIndex: -1,
    editedItem: {
      title: "",
      type_manual: "",
      description: "",
      department: "",
      employee: "",
      start_date: null,
      end_date: null
    },
    defaultItem: {
      title: "",
      description: "",
      type: "",
      supervisor: "",
      company_id: "",
      start_date: null,
      end_date: null
    },
    response: "",
    data: [],
    reporters: [],
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
      this.type_manual = false;
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
    this.getReporters();
  },

  methods: {
    can(per) {
      return true;
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },
    onScroll() {
      this.scrollInvoked++;
    },

    status(status) {
      let payload = {
        id: this.leave_id,
        approved_by: this.$auth.user.employee.id,
        is_approved: status
      };

      this.$axios
        .post(`leave-status`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.getDataFromApi();
            this.viewItem(data.record);
            this.snackbar = data.status;
            this.response = data.message;
            this.close();
            this.errors = [];
            this.search = "";
          }
        })
        .catch(res => console.log(res));
    },

    changeType(item) {
      if (item == "other") {
        this.type_manual = true;
      }
    },

    getReporters() {
      let id = this.$auth.user.employee.id;
      this.$axios.get(`reporter_by_employee/${id}`).then(({ data }) => {
        this.reporters = data;
      });
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      const { page, itemsPerPage } = this.options;
      let id = this.$auth.user.employee.id;
      // console.log(id);
      // return;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.employee.company_id
        }
      };

      this.$axios.get(`${url}/${id}?page=${page}`, options).then(({ data }) => {
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
    },

    viewItem(item) {
      this.view_notification = true;
      if (item.approved_by && item.is_approved == 1) {
        this.approved_by = item.approved_by.first_name;
        this.approved_status = "Approved By";
      } else if (item.approved_by && item.is_approved == 2) {
        this.approved_by = item.approved_by.first_name;
        this.approved_status = "Rejectd By";
      } else if (item.is_approved == 0) {
        this.approved_status = "Pending";
      }

      this.leave_id = item.id;
      this.dept = item.employee.department.name;
      this.des = item.description;
      this.title = item.title;
      this.desDate = item.created_at;
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
              this.getDataFromApi();
              this.snackbar = data.status;
              this.ids = [];
              this.response = "Selected records has been deleted";
            }
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

    save() {
      let editedItem = {
        title: this.editedItem.title.toLowerCase(),
        description: this.editedItem.description,
        start_date: this.editedItem.start_date,
        end_date: this.editedItem.end_date,
        type:
          this.editedItem.type == "other"
            ? this.editedItem.type_manual
            : this.editedItem.type,
        supervisor: this.editedItem.supervisor,
        company_id: this.$auth.user.employee.company_id,
        employee_id: this.$auth.user.employee.employee_id
      };

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
            this.type_manual = false;
          }
        })
        .catch(res => console.log(res));
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

<template>
  <div v-if="can(`policy_access`)">
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
            v-if="can(`policy_deleted`)"
            small
            color="error"
            class="mr-2 mb-2"
            @click="delteteSelectedRecords"
          >
            Delete Selected Records</v-btn
          >

          <v-btn
            v-if="can(`policy_create`)"
            small
            color="primary"
            @click="dialog = true"
            class="mb-2"
            >{{ Model }} +
          </v-btn>
        </div>
      </v-col>
    </v-row>

    <v-dialog v-model="dialog" max-width="60%">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>{{ formTitle }} {{ Model }} List</span>
        </v-toolbar>

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
                <ClientOnly>
                  <tiptap-vuetify
                    class="tiptap-icon"
                    v-model="editedItem.body"
                    :extensions="extensions"
                    v-scroll.self="onScroll"
                    max-height="400"
                    :toolbar-attributes="{
                      color: 'background'
                    }"
                  />
                  <template #placeholder> Loading... </template>
                </ClientOnly>
              </v-col>
              <span v-if="errors && errors.body" class="text-danger mt-2">{{
                errors.body[0]
              }}</span>
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
            <span class="pb-0"><small>Policy Details</small></span>
          </div>
          <div class="w-50 text-right">
            <span class="pb-0"
              ><small>Created : {{ desDate }}</small></span
            >
          </div>
        </v-toolbar>
        <v-card-text>
          <v-container>
            <h3>{{ title }}</h3>
            <v-divider></v-divider>
            <div v-html="body"></div>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-row>
      <v-col md="12">
        <v-data-table
          v-if="can(`policy_view`)"
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
          <template v-slot:item.body="{ item }">
            <div v-html="item.body"></div>
          </template>
          <template v-slot:item.action="{ item }">
            <v-icon
              v-if="can(`policy_edit`)"
              color="secondary"
              small
              class="mr-2"
              @click="editItem(item)"
            >
              mdi-pencil
            </v-icon>
            <v-icon
              v-if="can(`policy_show`)"
              color="secondary"
              small
              class="mr-2"
              @click="viewItem(item)"
            >
              mdi-eye
            </v-icon>
            <v-icon
              v-if="can(`policy_delete`)"
              color="error"
              small
              @click="deleteItem(item)"
            >
              {{ item.policy === "customer" ? "" : "mdi-delete" }}
            </v-icon>
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
  components: { TiptapVuetify },
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
    options: {},
    Model: "Policy",
    endpoint: "policy",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    headers: [
      { text: "Body", align: "left", sortable: false, value: "body" },
      { text: "Title", align: "left", sortable: false, value: "title" },
      { text: "Actions", align: "center", value: "action", sortable: false }
    ],
    editedIndex: -1,
    editedItem: { title: "", body: "" },
    defaultItem: { title: "", body: "" },
    title: "",
    body: "",
    des: "",
    desDate: "",
    response: "",
    view_notification: false,
    data: [],
    errors: []
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
  },

  methods: {
    selectImage() {
      // When doing an asynchronous upload, you can set the src property to the value provided by the server (backend).
      this.$emit("select-file", {
        src: "/path/to/image.jpg",
        alt: "Uploaded image"
      });
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },
    onScroll() {
      this.scrollInvoked++;
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
    },

    viewItem(item) {
      this.view_notification = true;
      this.body = item.body;
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
      let payload = {
        title: this.editedItem.title.toLowerCase(),
        body: this.editedItem.body.toLowerCase(),
        company_id: this.$auth.user.company.id
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, payload)
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
          .catch(res => console.log(res));
      }
    }
  }
};
</script>

<style>
.tiptap-vuetify-editor__content {
  min-height: 400px !important;
}

.ProseMirror .ProseMirror-focused {
  height: 400px !important;
}

.tiptap-icon .v-icon {
  color: white !important;
}
.tiptap-icon .v-btn--icon {
  color: white !important;
}
</style>

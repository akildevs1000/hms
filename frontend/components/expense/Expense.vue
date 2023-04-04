<template>
  <div>
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
        <v-spacer></v-spacer>
        <v-btn class="float-right py-3" @click="expenseDialog = true" color="primary">
          <v-icon color="white" small class="py-5">mdi-plus</v-icon>
          Add
        </v-btn>
      </v-col>
    </v-row>

    <v-row>

      <v-col xs="12" sm="12" md="3" cols="12">
        <v-text-field dense outlined placeholder="Search..." solo flat @input="searchIt" v-model="search"
          hide-details></v-text-field>
      </v-col>
      <v-col md="3">
        <v-menu v-model="from_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
          offset-y min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field v-model="from_date" readonly v-bind="attrs" v-on="on" dense outlined :hide-details="true" flat
              label="From"></v-text-field>
          </template>
          <v-date-picker v-model="from_date" @input="from_menu = false" @change="commonMethod"></v-date-picker>
        </v-menu>
      </v-col>
      <v-col md="3">
        <v-menu v-model="to_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y
          min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field v-model="to_date" readonly v-bind="attrs" v-on="on" dense outlined flat label="To"
              :hide-details="true"></v-text-field>
          </template>
          <v-date-picker v-model="to_date" @input="to_menu = false" @change="commonMethod"></v-date-picker>
        </v-menu>
      </v-col>
    </v-row>

    <v-dialog v-model="imgView" max-width="80%">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Preview</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="imgView = false">mdi mdi-close-box</v-icon>
        </v-toolbar>
        <v-container>
          <!-- <ImagePreview :docObj="documentObj"></ImagePreview> -->
          <ImagePreview :docObj="documentObj" />
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="expenseDialog" max-width="40%">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <v-toolbar color="background" dense flat dark>
            <span>{{ formTitle }} {{ Model }}</span>
          </v-toolbar>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="expenseDialog = false">
            mdi mdi-close-box
          </v-icon>
        </v-toolbar>
        <v-container>
          <v-row class="mt-0 px-2">
            <v-col cols="6">
              <v-text-field v-model="editedItem.voucher" placeholder="Voucher" label="Voucher" outlined
                :hide-details="true" dense>
              </v-text-field>
              <span v-if="errors && errors.voucher" class="error--text">
                {{ errors.voucher[0] }}
              </span>
            </v-col>
            <v-col cols="6" class="m-0 p-0">
              <v-text-field v-model="editedItem.item" placeholder="Item" label="Item" outlined :hide-details="true"
                dense></v-text-field>
              <span v-if="errors && errors.item" class="error--text">
                {{ errors.item[0] }}
              </span>
            </v-col>
            <v-col cols="6" class="m-0 p-0">
              <v-text-field v-model="editedItem.amount" placeholder="Amount" label="Amount" :hide-details="true"
                @keyup="calSum" outlined dense type="number"></v-text-field>
              <span v-if="errors && errors.amount" class="error--text">
                {{ errors.amount[0] }}
              </span>
            </v-col>
            <v-col cols="6">
              <v-text-field v-model="editedItem.qty" placeholder="QTY" label="QTY" :hide-details="true" outlined dense
                @keyup="calSum" type="number"></v-text-field>
              <span v-if="errors && errors.qty" class="error--text">{{
                errors.qty[0]
              }}</span>
            </v-col>
            <v-col cols="6">
              <v-text-field v-model="editedItem.total" placeholder="Total Amount" label="Total Amount" readonly
                :hide-details="true" outlined dense type="number"></v-text-field>
              <span v-if="errors && errors.amount" class="error--text">{{
                errors.amount[0]
              }}</span>
            </v-col>
            <v-col cols="6">
              <v-autocomplete v-model="editedItem.payment_modes" :items="[
                { id: 1, name: 'Cash' },
                { id: 2, name: 'Card' },
                { id: 3, name: 'Online' },
                { id: 4, name: 'Bank' },
                { id: 5, name: 'UPI' },
                { id: 6, name: 'Cheque' },
              ]" item-text="name" item-value="id" placeholder="Select Payment Mode" label="Select Payment Mode"
                outlined :hide-details="true" dense>
              </v-autocomplete>
              <span v-if="errors && errors.department_id" class="error--text">{{
                errors.department_id[0]
              }}</span>
            </v-col>

            <v-col cols="12" v-if="is_management">
              <v-autocomplete v-model="editedItem.user" :items="['Ariff', 'Ansari']" item-text="name" item-value="id"
                placeholder="Select User" label="Select Payment Mode" outlined :hide-details="true" dense>
              </v-autocomplete>
              <span v-if="errors && errors.department_id" class="error--text">{{
                errors.department_id[0]
              }}</span>
            </v-col>

            <v-col cols="6" v-if="editedItem.payment_modes != 1">
              <v-text-field v-model="editedItem.reference" placeholder="Reference" label="Reference" :hide-details="true"
                outlined dense type="text"></v-text-field>
              <span v-if="errors && errors.amount" class="error--text">{{
                errors.amount[0]
              }}</span>
            </v-col>
            <v-col :md="formTitle === 'New' ? 12 : 12">
              <v-file-input v-model="editedItem.document" color="primary" counter placeholder="Select your files"
                :hide-details="true" outlined :show-size="1000">
                <template v-slot:selection="{ index, text }">
                  <v-chip v-if="index < 2" color="primary" dark label small>
                    {{ text }}
                  </v-chip>

                  <span v-else-if="index === 2" class="text-overline grey--text text--darken-3 mx-2">
                    +{{ editedItem.document.length - 2 }} File(s)
                  </span>
                </template>
              </v-file-input>
              <small class="red--text" v-if="errors && errors.document">
                {{ errors && errors.document ? errors.document[0] : "" }}
              </small>
            </v-col>
            <!-- <div class="mt-2 ml-4" v-if="getDocType(editedItem.document)">
              <v-btn
                small
                dark
                class="primary lg-pt-4 lg-pb-4 doc-btn"
                @click="preview(editedItem.document)"
              >
                Preview
                <v-icon right dark>mdi-file</v-icon>
              </v-btn>
            </div> -->
            <v-col cols="12">
              <v-textarea filled placeholder="Description" label="Description" :hide-details="true"
                v-model="editedItem.description" outlined dense rows="4" row-height="15"></v-textarea>
              <span v-if="errors && errors.description" class="error--text">{{
                errors.description[0]
              }}</span>
            </v-col>
            <v-card-actions>
              <v-btn class="primary" :loading="loading" @click="save">Save</v-btn>
            </v-card-actions>
          </v-row>
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row>
      <v-col md="12" class="float-right">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span> Today {{ Model }} List</span>
            <v-spacer></v-spacer>
            <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn class="ma-0" x-small :ripple="false" text v-bind="attrs" v-on="on"
                  @click="process('expense_report_print')">
                  <v-icon class="white--text">mdi-printer-outline</v-icon>
                </v-btn>
              </template>
              <span>PRINT</span>
            </v-tooltip>

            <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on" @click="process('expense_report_download')">
                  <v-icon class="white--text">mdi-download-outline</v-icon>
                </v-btn>
              </template>
              <span> DOWNLOAD </span>
            </v-tooltip>
          </v-toolbar>
          <table>
            <tr style="font-size: 12px">
              <!-- <th v-for="(item, index) in   headers" :key="index" style="font-size: 12px">
                <span v-html="item.text"></span>
              </th> -->
              <th>#</th>
              <th>Date</th>
              <th>Voucher</th>
              <th v-if="is_management">User</th>
              <th>Item</th>
              <th>QTY</th>
              <th>Amount</th>
              <th>Total</th>
              <th>Mode</th>
              <th>Reference</th>
              <th>Document</th>
            </tr>
            <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
              color="primary"></v-progress-linear>
            <tr v-for="(item, index) in   data" :key="index" style="font-size: 12px">
              <td>{{ ++index }}</td>
              <td>{{ item.created_at }}</td>
              <td>{{ item.voucher || "" }}</td>
              <td v-if="is_management">{{ item.user || "" }}</td>
              <td>{{ item.item || "" }}</td>
              <td>{{ item.qty || "" }}</td>
              <td>{{ item.amount || "---" }}</td>
              <td>{{ item.total || "---" }}</td>
              <td>{{ (item && item.payment_mode.name) || "---" }}</td>
              <td>{{ (item && item.reference) || "---" }}</td>
              <td>
                <v-btn v-if="item.document" small dark class="primary lg-pt-4 lg-pb-4 doc-btn"
                  @click="preview(item.document)">
                  Preview
                  <v-icon right dark>mdi-file</v-icon>
                </v-btn>
                <span v-else> --- </span>
              </td>
              <td class="text-center">
                <v-menu bottom left>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn dark-2 icon v-bind="attrs" v-on="on">
                      <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                  </template>
                  <v-list width="120" dense>
                    <v-list-item @click="editItem(item)">
                      <v-list-item-title style="cursor: pointer">
                        <v-icon color="secondary" small> mdi-pencil </v-icon>
                        Edit
                      </v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="deleteItem(item)">
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
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col md="12" class="float-right">
        <div class="float-right">
          <v-pagination v-model="pagination.current" :length="pagination.total" @input="onPageChange"
            :total-visible="12"></v-pagination>
        </div>
      </v-col>

    </v-row>
  </div>
</template>
<script>
import ImagePreview from '../images/ImagePreview.vue';
export default {
  props: [
    'is_management'
  ],
  components: {
    ImagePreview,
  },
  data: () => ({
    Model: "Expense",


    from_date: "",
    from_menu: false,

    to_date: "",
    to_menu: false,

    pagination: {
      current: 1,
      total: 0,
      per_page: 50,
      status: "-1",
    },
    options: {},
    endpoint: "expense",
    search: "",
    snackbar: false,
    dialog: false,
    expenseDialog: false,
    data: [],
    loading: false,
    imgView: false,
    documentObj: {
      fileExtension: null,
      file: null,
    },
    total: 0,
    headers: [
      { text: "#" },
      { text: "Date" },
      { text: "Voucher" },
      { text: "Item" },
      { text: "QTY" },
      { text: "Amount" },
      { text: "Total" },
      { text: "Mode" },
      { text: "Reference" },
      { text: "Document" },
    ],
    editedIndex: -1,
    response: "",
    errors: [],
    editedItem: {
      item: null,
      amount: 0,
      qty: "",
      payment_modes: 1,
      voucher: "",
      description: "",
      total: 0,
      reference: "",
      document: null,
      user: "",
    },
  }),

  watch: {
    expenseDialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },

  created() {
    this.loading = true;
    this.getDataFromApi();
  },

  methods: {
    onPageChange() {
      this.getDataFromApi();
    },
    can(permission) {
      let user = this.$auth;
      return;
      return (
        (user && user.permissions.some((e) => e.permission == permission)) ||
        user.master
      );
    },

    process(type) {
      let comId = this.$auth.user.company.id; //company id
      let is_management = this.is_management;
      let url = process.env.BACKEND_URL + `${type}?company_id=${comId}&is_management=${is_management}`;
      console.log(url);
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}`);
      document.body.appendChild(element);
      element.click();
    },

    close() {
      this.expenseDialog = false;
      this.editedItem = Object.assign({}, this.defaultItem);
      this.editedItem.payment_modes = 1;
      this.editedIndex = -1;
    },

    calSum() {
      let tot =
        parseFloat(this.editedItem.amount) * parseInt(this.editedItem.qty);
      this.editedItem.total = tot.toFixed(2);
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

    getDocType(doc) {
      return typeof doc == "string" ? true : false;
    },

    preview(doc) {
      // let file = this.editedItem.document ?? null;
      console.log(doc);
      let file = doc ?? null;
      const fileExtension = file.split(".").pop().toLowerCase();
      fileExtension == "pdf" ? (this.isPdf = true) : (this.isImg = true);
      this.documentObj = {
        fileExtension: fileExtension,
        file: file,
      };
      this.imgView = true;
    },

    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      console.log(this.editedItem);
      this.expenseDialog = true;
    },

    commonMethod() {
      this.getDataFromApi();
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          status: this.pagination.status,
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          from: this.from_date,
          to: this.to_date,
          is_management: this.is_management,
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
      if (e.length == 0) {
        this.getDataFromApi(this.endpoint);
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },

    mapper(obj) {
      let payload = new FormData();
      for (let x in obj) {
        if (obj[x]) {
          console.log(x);
          payload.append(x, obj[x]);
        }
      }

      if (this.editedIndex > -1) {
        payload.append("_method", "PUT");
      }

      if (this.is_management) {
        payload.append("is_management", "1");
      }

      payload.append("company_id", this.$auth.user.company.id);
      return payload;
    },

    save() {
      this.loading = true;
      let payload = this.mapper(this.editedItem);
      if (this.editedIndex > -1) {
        this.$axios
          .post(this.endpoint + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
              this.loading = false;
            } else {
              const index = this.data.findIndex(
                (item) => item.id == this.editedItem.id
              );
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.loading = false;
              this.expenseDialog = false;
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, payload)
          .then(({ data }) => {
            console.log(data);
            if (!data.status) {
              this.errors = data.errors;
              this.loading = false;
            } else {
              this.getDataFromApi();
              this.snackbar = true;
              this.response = "Expenses successfully added";
              this.close();
              this.errors = [];
              this.search = "";
              this.loading = false;
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
  padding: 7px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}
</style>

<template >
  <div
    v-if="(can('accounts_expences_access') && can('accounts_expences_view')) || (can('management_expenses_access') && can('management_expenses_view'))">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-0 mb-0">
      <v-col cols="6">
        <h3>{{ Model }}</h3>

      </v-col>
      <v-col cols="6">
        <v-spacer></v-spacer>
        <v-btn v-if="can('accounts_expences_create') || can('management_expenses_create')" class="float-right py-3"
          @click="expenseDialog = true" color="primary">
          <v-icon color="white" small class="py-5">mdi-plus</v-icon>
          Add
        </v-btn>
      </v-col>
    </v-row>
    <v-row>
      <div class="col-xl-3 my-0 py-0 col-lg-6 text-uppercase">
        <div class="card px-2" style="background-color: #800000">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-ddoor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Total</h6>
              <span class="data-1"> {{ $auth.user.company.currency }} {{ getPriceFormat(totalAmount) || 0 }}</span>
            </div>
          </div>
        </div>
      </div>
    </v-row>
    <!-- <v-row>

      <v-col cols="12" md="6" xl="3">
        <v-card class="px-2" color="#800000">
          <v-card-text class="text-center white--text">
            <h6 class="text-uppercase">Total</h6>
            <div class="display-1 mb-2">₹{{ totalAmount || 0 }}</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row> -->

    <v-row>
      <v-col xs="12" sm="12" md="2" cols="12">


        <v-text-field dense outlined placeholder="Search..." @input="commonMethod" v-model="search"
          hide-details></v-text-field>

      </v-col>
      <v-col xs="12" sm="12" md="2" cols="12">
        <DateRangePicker key="postings" :disabled="false" :DPStart_date="from_date" :DPEnd_date="to_date"
          column="date_range" @selected-dates="handleDatesFilter" />
      </v-col>
      <!-- <v-col md="3">
        <div class="ml-4">Filter</div>
        <v-col md="12">
          <v-select v-model="filterType" :items="[
            {
              id: 1,
              name: 'Today',
            },
            {
              id: 2,
              name: 'Yesterday',
            },
            {
              id: 3,
              name: 'This Week',
            },
            {
              id: 4,
              name: 'This Month',
            },
            {
              id: 5,
              name: 'Custom',
            },
          ]" dense placeholder="Type" outlined :hide-details="true" item-text="name" item-value="id"
            @change="commonMethod"></v-select></v-col>
      </v-col>

      <v-col md="3" v-if="filterType == 5">
        <div class="ml-4">From</div>
        <v-col cols="12" sm="12" md="12">
          <v-menu v-model="from_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
            offset-y min-width="auto">
            <template v-slot:activator="{ on, attrs }">
              <v-text-field v-model="from_date" readonly v-bind="attrs" v-on="on" outlined dense
                :hide-details="true"></v-text-field>
            </template>
            <v-date-picker v-model="from_date" @input="from_menu = false" @change="commonMethod"></v-date-picker>
          </v-menu>
        </v-col>
      </v-col>
      <v-col md="3" v-if="filterType == 5">
        <div class="ml-4">To</div>
        <v-col cols="12" sm="12" md="12">
          <v-menu v-model="to_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
            offset-y min-width="auto">
            <template v-slot:activator="{ on, attrs }">
              <v-text-field v-model="to_date" readonly v-bind="attrs" v-on="on" outlined dense
                :hide-details="true"></v-text-field>
            </template>
            <v-date-picker v-model="to_date" @input="to_menu = false" @change="commonMethod"></v-date-picker>
          </v-menu>
        </v-col>
      </v-col> -->
    </v-row>

    <v-dialog v-model="imgView" :width="previewImageWidth">
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
              <v-autocomplete v-model="editedItem.user" :items="['Nadia', 'Ariff', 'Ansari']" item-text="name"
                item-value="id" placeholder="Select User" label="Select User" outlined :hide-details="true" dense>
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

            <v-col :md="3">
              <v-file-input dense v-model="editedItem.document" color="primary" counter placeholder="Invoice"
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

            <v-col :md="3">
              <v-file-input dense v-model="editedItem.document1" color="primary" counter placeholder="Receipt"
                :hide-details="true" outlined :show-size="1000">
                <template v-slot:selection="{ index, text }">
                  <v-chip v-if="index < 2" color="primary" dark label small>
                    {{ text }}
                  </v-chip>

                  <span v-else-if="index === 2" class="text-overline grey--text text--darken-3 mx-2">
                    +{{ editedItem.document1.length - 2 }} File(s)
                  </span>
                </template>
              </v-file-input>
              <small class="red--text" v-if="errors && errors.document1">
                {{ errors && errors.document1 ? errors.document1[0] : "" }}
              </small>
            </v-col>

            <v-col :md="3">
              <v-file-input dense v-model="editedItem.document2" color="primary" counter placeholder="Bank Slip"
                :hide-details="true" outlined :show-size="1000">
                <template v-slot:selection="{ index, text }">
                  <v-chip v-if="index < 2" color="primary" dark label small>
                    {{ text }}
                  </v-chip>
                  <span v-else-if="index === 2" class="text-overline grey--text text--darken-3 mx-2">
                    +{{ editedItem.document2.length - 2 }} File(s)
                  </span>
                </template>
              </v-file-input>
              <small class="red--text" v-if="errors && errors.document2">
                {{ errors && errors.document2 ? errors.document2[0] : "" }}
              </small>
            </v-col>

            <v-col :md="3">
              <v-file-input dense v-model="editedItem.document3" color="primary" counter placeholder="Other"
                :hide-details="true" outlined :show-size="1000">
                <template v-slot:selection="{ index, text }">
                  <v-chip v-if="index < 2" color="primary" dark label small>
                    {{ text }}
                  </v-chip>

                  <span v-else-if="index === 2" class="text-overline grey--text text--darken-3 mx-2">
                    +{{ editedItem.document3.length - 2 }} File(s)
                  </span>
                </template>
              </v-file-input>
              <small class="red--text" v-if="errors && errors.document3">
                {{ errors && errors.document3 ? errors.document3[0] : "" }}
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
              <th>Description</th>
              <th>Document</th>
            </tr>
            <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
              color="primary"></v-progress-linear>
            <tr v-for="(item, index) in data" :key="index" style="font-size: 12px">
              <td>{{ ++index }}</td>
              <td style="width: 100px;">{{ item.created_at }}</td>
              <td>{{ item.voucher || "" }}</td>
              <td v-if="is_management">{{ item.user || "" }}</td>
              <td>{{ item.item || "" }}</td>
              <td>{{ item.qty || "" }}</td>
              <td>{{ item.amount || "---" }}</td>
              <td>{{ item.total || "---" }}</td>
              <td>{{ (item && item.payment_mode.name) || "---" }}</td>
              <td>{{ (item && item.reference) || "---" }}</td>
              <td>
                <v-tooltip bottom color="primary">
                  <template v-slot:activator="{ on, attrs }">
                    <span v-bind="attrs" v-on="on">{{
                      limitedStr(item && item.description) || "---"
                    }}</span>
                  </template>
                  <span>{{ (item && item.description) || "---" }}</span>
                </v-tooltip>
              </td>
              <td>
                <v-icon v-if="item.document" @click="preview(item.document)" right>mdi
                  mdi-alpha-i-circle
                </v-icon>
                <span v-else> --- </span>

                <v-icon v-if="item.document1" @click="preview(item.document1)" right> mdi-alpha-r-circle</v-icon>
                <span v-else> --- </span>

                <v-icon v-if="item.document2" @click="preview(item.document2)" right> mdi-alpha-b-circle</v-icon>
                <span v-else> --- </span>

                <v-icon v-if="item.document3" @click="preview(item.document3)" right> mdi-alpha-o-circle</v-icon>
                <span v-else> --- </span>
              </td>
              <td class="text-center">
                <v-menu bottom left
                  v-if="can('accounts_expences_edit') || can('accounts_expences_delete') || can('management_expenses_edit') || can('management_expenses_delete')">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn dark-2 icon v-bind="attrs" v-on="on">
                      <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                  </template>
                  <v-list width="120" dense>
                    <v-list-item v-if="can('accounts_expences_edit') || can('management_expenses_edit')"
                      @click="editItem(item)">
                      <v-list-item-title style="cursor: pointer">
                        <v-icon color="secondary" small> mdi-pencil </v-icon>
                        Edit
                      </v-list-item-title>
                    </v-list-item>
                    <v-list-item v-if="can('accounts_expences_delete') || can('management_expenses_delete')"
                      @click="deleteItem(item)">
                      <v-list-item-title style="cursor: pointer">
                        <v-icon color="error" small> mdi-delete </v-icon>
                        Delete
                      </v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </td>
            </tr>
            <tr style="background-color:white;font-size: 12px">
              <td colspan="7" class="text-right">
                <b>Total:</b>{{ totalAmount || 0 }}
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

  <NoAccess v-else />
</template>
<script>
import ImagePreview from "../images/ImagePreview.vue";
import CustomFilter from "../filter/CustomFilter.vue";
export default {
  props: ["is_management"],
  components: {
    CustomFilter,
    ImagePreview,
  },
  data: () => ({
    previewImageWidth: "500px",
    Model: "Expense",

    from_date: "",
    from_menu: false,

    to_date: "",
    to_menu: false,
    filterType: 1,
    pagination: {
      current: 1,
      total: 0,
      per_page: 200,
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
    search: "",
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
      { text: "Description" },
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
      document1: null,
      document2: null,
      document3: null,
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
    totalAmount() {
      let sum = 0;
      this.data.map((e) => (sum += parseFloat(e.total)));
      return sum.toFixed(2);
    },

    currentDate() {
      return new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10);
    },

    week() {
      const today = new Date();
      const dayOfWeek = today.getDay(); // Sunday = 0, Monday = 1, ..., Saturday = 6
      const startOfWeek = new Date(
        today.getFullYear(),
        today.getMonth(),
        today.getDate() - dayOfWeek
      );
      const endOfWeek = new Date(
        today.getFullYear(),
        today.getMonth(),
        startOfWeek.getDate() + 6
      );

      return [
        startOfWeek.toISOString().slice(0, 10),
        endOfWeek.toISOString().slice(0, 10),
      ];
    },
  },

  created() {
    this.month = new Date().getMonth();
    this.year = new Date().getFullYear();
    this.from_date = this.formatDate(new Date(this.year, this.month, 1));
    this.to_date = this.formatDate(new Date(this.year, this.month + 1, 0));

    this.loading = true;
    this.commonMethod();
  },

  methods: {
    getPriceFormat(price) {

      return parseFloat(price).toLocaleString('en-IN', {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2,
      });
    },
    handleDatesFilter(dates) {
      this.filterType = 5;
      this.from_date = dates[0];
      this.to_date = dates[1];
      if (this.from_date && this.to_date)
        this.commonMethod();
    },
    formatDate(date) {
      var day = date.getDate();
      var month = date.getMonth() + 1; // Months are zero-based
      var year = date.getFullYear();
      return year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
    },
    limitedStr(str) {
      if (!str) return "---";
      return str.slice(0, 10) + "...";
    },

    onPageChange() {
      this.commonMethod();
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },

    process(type) {
      let comId = this.$auth.user.company.id; //company id
      let is_management = this.is_management;
      let url =
        process.env.BACKEND_URL +
        `${type}?company_id=${comId}&is_management=${is_management}`;
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
      this.commonMethod();
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
      fileExtension == "pdf" ? (this.previewImageWidth = "70%") : this.previewImageWidth = "500px";

      this.documentObj = {
        fileExtension: fileExtension,
        file: file + "?t=" + Math.random(),
      };
      this.imgView = true;
    },

    editItem(item) {
      console.log(item);
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      console.log(this.editedItem);
      this.expenseDialog = true;
    },

    getFirstAndLastDay() {
      const currentDate = new Date();
      const day = currentDate.getDate();
      const month = (currentDate.getMonth() + 1).toString().padStart(2, "0");
      const year = currentDate.getFullYear();
      const last = new Date(year, month, 0).getDate().toString().padStart(2, "0");

      let firstDay = `${year}-${month}-0${1}`;

      let lastDayFirst = last > 9 ? `${last}` : `0${last}`;

      let lastDay = `${year}-${month}-${lastDayFirst}`;

      return [
        firstDay,
        lastDay
      ]
    },

    commonMethod() {
      const today = new Date();
      switch (this.filterType) {
        case 1:
          this.from_date = this.currentDate;
          this.to_date = this.currentDate;
          break;
        case 2:
          this.from_date = new Date(Date.now() - 86400000)
            .toISOString()
            .slice(0, 10);
          this.to_date = new Date(Date.now() - 86400000)
            .toISOString()
            .slice(0, 10);
          break;
        case 3:
          this.from_date = this.week[0];
          this.to_date = this.week[1];
          break;
        case 4:
          this.from_date = this.getFirstAndLastDay()[0];
          this.to_date = this.getFirstAndLastDay()[1];
          break;

        // default:
        //   this.from_date = new Date().toJSON().slice(0, 10);
        //   this.to_date = new Date().toJSON().slice(0, 10);
        //   break;
      }
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
          search: this.search,
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
    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete?"
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            this.getDataFromApi();
            this.snackbar = data.status;
            this.response = data.message;
          })
          .catch((err) => console.log(err));
    },
    // searchIt(e) {
    //   if (e.length == 0) {
    //     this.getDataFromApi(this.endpoint);
    //   } else if (e.length > 2) {
    //     this.getDataFromApi(`${this.endpoint}/search/${e}`);
    //   }
    // },

    mapper(obj) {
      let payload = new FormData();
      for (let x in obj) {
        if (obj[x]) {
          console.log(obj);
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
      // this.loading = true;
      let payload = this.mapper(this.editedItem);

      console.log(payload);
      // return;

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
<style scoped src="@/assets/dashtem.css"></style>
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

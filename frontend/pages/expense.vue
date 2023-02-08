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
      <v-col cols="6"> </v-col>
    </v-row>

    <v-row>
      <v-col xs="12" sm="12" md="3" cols="12">
        <v-select
          class="form-control"
          @change="getDataFromApi(`expense`)"
          v-model="pagination.per_page"
          :items="[10, 25, 50, 100]"
          placeholder="Per Page Records"
          solo
          hide-details
          flat
        ></v-select>
      </v-col>
      <v-col xs="12" sm="12" md="3" cols="12">
        <v-text-field
          class="form-control py-0 custom-text-box floating shadow-none"
          placeholder="Search..."
          solo
          flat
          @input="searchIt"
          v-model="search"
          hide-details
        ></v-text-field>
      </v-col>
    </v-row>

    <v-row>
      <v-col md="4" lg="4">
        <v-card elevation="0">
          <v-toolbar color="background" dense flat dark>
            <span>Create {{ Model }}</span>
          </v-toolbar>
          <v-divider class="py-0 my-0"></v-divider>
          <v-card-text>
            <v-container>
              <v-row class="mt-2">
                <v-col cols="12">
                  <v-text-field
                    v-model="editedItem.voucher"
                    placeholder="Voucher"
                    outlined
                    :hide-details="true"
                    dense
                  ></v-text-field>
                  <span v-if="errors && errors.voucher" class="error--text">{{
                    errors.voucher[0]
                  }}</span>
                </v-col>
                <v-col cols="12" class="m-0 p-0">
                  <v-text-field
                    v-model="editedItem.item"
                    placeholder="Item"
                    outlined
                    :hide-details="true"
                    dense
                  ></v-text-field>
                  <span v-if="errors && errors.item" class="error--text">{{
                    errors.item[0]
                  }}</span>
                </v-col>
                <v-col cols="12" class="m-0 p-0">
                  <v-text-field
                    v-model="editedItem.amount"
                    placeholder="Amount"
                    :hide-details="true"
                    @keyup="calSum"
                    outlined
                    dense
                    type="number"
                  ></v-text-field>
                  <span v-if="errors && errors.amount" class="error--text">{{
                    errors.amount[0]
                  }}</span>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    v-model="editedItem.qty"
                    placeholder="QTY"
                    :hide-details="true"
                    outlined
                    dense
                    @keyup="calSum"
                    type="number"
                  ></v-text-field>
                  <span v-if="errors && errors.qty" class="error--text">{{
                    errors.qty[0]
                  }}</span>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    v-model="editedItem.total"
                    placeholder="Total Amount"
                    readonly
                    :hide-details="true"
                    outlined
                    dense
                    type="number"
                  ></v-text-field>
                  <span v-if="errors && errors.amount" class="error--text">{{
                    errors.amount[0]
                  }}</span>
                </v-col>
                <v-col cols="12">
                  <v-autocomplete
                    v-model="editedItem.payment_modes"
                    :items="[
                      { id: 1, name: 'Cash' },
                      { id: 2, name: 'Card' },
                      { id: 3, name: 'Online' },
                      { id: 4, name: 'Bank' },
                      { id: 5, name: 'UPI' },
                      { id: 6, name: 'Cheque' },
                    ]"
                    item-text="name"
                    item-value="id"
                    placeholder="Select Payment Mode"
                    outlined
                    :hide-details="true"
                    dense
                  >
                  </v-autocomplete>
                  <span
                    v-if="errors && errors.department_id"
                    class="error--text"
                    >{{ errors.department_id[0] }}</span
                  >
                </v-col>
                <v-col cols="12" v-if="editedItem.payment_modes != 1">
                  <v-text-field
                    v-model="editedItem.reference"
                    placeholder="Reference"
                    :hide-details="true"
                    outlined
                    dense
                    type="text"
                  ></v-text-field>
                  <span v-if="errors && errors.amount" class="error--text">{{
                    errors.amount[0]
                  }}</span>
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    filled
                    label="Description"
                    :hide-details="true"
                    v-model="editedItem.description"
                    outlined
                  ></v-textarea>
                  <span
                    v-if="errors && errors.description"
                    class="error--text"
                    >{{ errors.description[0] }}</span
                  >
                </v-col>
                <v-card-actions>
                  <v-btn class="primary" @click="save">Save</v-btn>
                </v-card-actions>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col md="8" class="float-right">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span> Today {{ Model }} List</span>
          </v-toolbar>
          <table>
            <tr>
              <th
                v-for="(item, index) in headers"
                :key="index"
                style="font-size: 12px"
              >
                <span v-html="item.text"></span>
              </th>
            </tr>
            <v-progress-linear
              v-if="loading"
              :active="loading"
              :indeterminate="loading"
              absolute
              color="primary"
            ></v-progress-linear>
            <tr
              v-for="(item, index) in data"
              :key="index"
              style="font-size: 12px"
            >
              <td>{{ ++index }}</td>
              <td>{{ item.voucher || "" }}</td>
              <td>{{ item.item || "" }}</td>
              <td>{{ item.qty || "" }}</td>
              <td>{{ item.amount || "" }}</td>
              <td>{{ item.total || "" }}</td>
              <td>{{ (item && item.payment_mode.name) || "" }}</td>
              <td>{{ (item && item.reference) || "" }}</td>
              <td>{{ item.created_at }}</td>
            </tr>
          </table>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col md="12" class="float-right">
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
  </div>
</template>
<script>
export default {
  data: () => ({
    Model: "Expense",

    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
      status: "-1",
    },
    options: {},
    endpoint: "expense",
    search: "",
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,
    headers: [
      { text: "#" },
      { text: "Voucher" },
      { text: "Item" },
      { text: "QTY" },
      { text: "Amount" },
      { text: "Total" },
      { text: "Mode" },
      { text: "Reference" },
      { text: "Date" },
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
    },
  }),

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
    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          status: this.pagination.status,
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
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
    save() {
      let payload = {
        ...this.editedItem,
        company_id: this.$auth.user.company.id,
      };

      this.$axios
        .post(this.endpoint, payload)
        .then(({ data }) => {
          console.log(data);
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.getDataFromApi();
            this.snackbar = true;
            this.response = "Expenses successfully added";
            this.close();
            this.errors = [];
            this.search = "";
          }
        })
        .catch((res) => console.log(res));
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

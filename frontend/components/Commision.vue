<template>
  <v-row class="pl-1 ma-2">
    <v-dialog v-model="add_popup" max-width="500px">
      <v-card>
        <v-card-actions>
          <span class="headline">{{ caps(`${Model}s`) }}</span>
          <v-spacer></v-spacer>
          <v-btn dark class="primary" fab @click="add" x-small>
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </v-card-actions>
        <v-card-text>
          <v-container>
            <v-row v-for="(item, index) in items" :key="index">
              <v-col cols="5">
                <v-text-field v-model="item.title" label="Title"></v-text-field>

                <span v-if="errors && errors.title" class="text-danger mt-2">{{
                  errors.title[0]
                }}</span>
              </v-col>

              <v-col cols="5">
                <v-text-field
                  type="number"
                  v-model="item.amount"
                  label="Amount"
                ></v-text-field>

                <span v-if="errors && errors.amount" class="text-danger mt-2">{{
                  errors.amount[0]
                }}</span>
              </v-col>

              <v-col cols="2">
                <v-icon class="mt-5" @click="removeItem(index)" color="error"
                  >mdi-delete</v-icon
                >
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="error" small @click="close((add_popup = false))">
            Cancel
          </v-btn>
          <v-btn :disabled="!items.length" class="primary" small @click="save"
            >Save</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="edit_popup" max-width="500px">
      <v-card>
        <v-card-actions>
          <span class="headline">{{ caps(`${Model}`) }}</span>
          <v-spacer></v-spacer>
        </v-card-actions>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="5">
                <v-text-field
                  v-model="editedItem.title"
                  label="Title"
                ></v-text-field>

                <span v-if="errors && errors.title" class="text-danger mt-2">{{
                  errors.title[0]
                }}</span>
              </v-col>

              <v-col cols="5">
                <v-text-field
                  type="number"
                  v-model="editedItem.amount"
                  label="Amount"
                ></v-text-field>

                <span v-if="errors && errors.amount" class="text-danger mt-2">{{
                  errors.amount[0]
                }}</span>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="error" small @click="close((edit_popup = false))">
            Cancel
          </v-btn>
          <v-btn
            :loading="loading"
            :disabled="!items.length"
            class="primary"
            small
            @click="update"
            >Save</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-col cols="6"> {{ Model }} List </v-col>
    <v-col cols="6" class="text-right">
      <v-avatar size="30">
        <v-icon
          v-if="can(`salary_commission_edit`)"
          @click="add_popup = true"
          small
          class="grey"
          color="secondary"
          >mdi-pencil</v-icon
        >
      </v-avatar>
    </v-col>
    <v-simple-table v-if="list.length > 0">
      <template v-slot:default>
        <thead>
          <tr>
            <th class="text-left">Title</th>
            <th class="text-left">Amount</th>
            <th class="text-left">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(d, index) in list" :key="index">
            <td>{{ caps(d.title) }}</td>
            <td>{{ d.amount }}</td>
            <td>
              <v-icon
                @click="edit_record(d)"
                small
                dark
                style="border-radius: 50%; padding: 5px"
                color="primary"
                >mdi-pencil</v-icon
              >
              <v-icon
                @click="delete_record(d)"
                small
                dark
                style="border-radius: 50%; padding: 5px"
                color="error"
                >mdi-delete</v-icon
              >
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
    <v-col v-else class="pa-4" cols="5">
      <v-list-item-title> No Record Found </v-list-item-title>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: ["employee_id"],
  data: () => ({
    Model: "Commision",
    endpoint: "commission",
    loading: true,
    add_popup: false,
    edit_popup: false,
    errors: [],
    snackbar: false,
    response: "",
    editedItem: {},
    items: [{ title: "", amount: "" }],
    list: [],
  }),
  async created() {
    this.getInfo();
  },
  watch: {
    add_popup(val) {
      val || this.close();
    },
  },
  methods: {
    caps(str) {
      return str.replace(/\b\w/g, (c) => c.toUpperCase());
    },
    add() {
      this.items.push({
        title: "",
        amount: "",
      });
    },
    removeItem(index) {
      this.items.splice(index, 1);
    },

    save() {
      let payload = {
        items: this.items,
        company_id: this.$auth?.user?.company?.id,
        employee_id: this.employee_id,
      };

      this.$axios
        .post(this.endpoint, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.items = [{ title: "", amount: "" }];

            this.getInfo();
            this.close((this.add_popup = false));
          }
        })
        .catch((e) => console.log(e));
    },

    update() {
      this.loading = true;

      this.$axios
        .put(`${this.endpoint}/${this.editedItem.id}`, this.editedItem)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;

            this.getInfo();

            setTimeout(() => {
              this.close((this.edit_popup = false));
            }, 2000);
          }
        })
        .catch((e) => console.log(e));
    },

    close(model = null) {
      this.errors = [];
      setTimeout(() => {
        this.loading = false;
      }, 500);
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    getInfo() {
      this.$axios
        .get(`${this.endpoint}/${this.employee_id}`)
        .then(({ data }) => {
          this.list = data;
          this.loading = false;
        });
    },
    edit_record(item) {
      this.edit_popup = true;

      this.editedItem = {
        id: item.id,
        title: item.title,
        amount: item.amount,
      };
    },
    delete_record(item) {
      confirm("Are you sure you want to delete this item?") &&
        this.$axios.delete(`${this.endpoint}/${item.id}`).then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            const index = this.list.indexOf(item);
            this.list.splice(index, 1);
          }
        });
    },
  },
};
</script>

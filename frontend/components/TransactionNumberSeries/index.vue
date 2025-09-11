<template>
  <v-card class="pa-4" flat max-width="800">
    <style>
      table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
      }

      td,
      th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
      }
    </style>
    <v-card-title class="text-h6"
      >Transaction Number Series Settings</v-card-title
    >

    <v-card-text v-if="!loading">
      <table style="width: 100%">
        <thead>
          <tr>
            <th>Module</th>
            <th>Prefix</th>
            <th>Starting Number</th>
            <th>Preview</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in modules" :key="index">
            <td>
              <v-text-field
                hide-details
                outlined
                v-model="item.module"
                required
              />
            </td>
            <td>
              <v-text-field
                hide-details
                outlined
                v-model="item.prefix"
                required
                @input="setItemPreview(item)"
              />
            </td>
            <td>
              <v-text-field
                hide-details
                outlined
                v-model="item.starting_number"
                required
                @input="setItemPreview(item)"
              />
            </td>
            <td>
              <v-text-field
                readonly
                hide-details
                outlined
                v-model="item.preview"
                required
              />
            </td>
          </tr>
        </tbody>
      </table>
      <br />
      <v-btn :loading="loading" color="primary" @click="submit">Submit</v-btn>
    </v-card-text>

    <!-- <pre>
        {{ modules }}
    </pre> -->

    <!-- Snackbar -->
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3000">
      {{ snackbar.text }}
    </v-snackbar>
  </v-card>
</template>

<script>
export default {
  data() {
    return {
      valid: true,
      company_id: null,
      modules: [
        {
          module: "Invoice",
          prefix: "Inv-",
          starting_number: 1001,
          preview: "Inv-1001",
        },
        {
          module: "Quotation",
          prefix: "Quo-",
          starting_number: 1001,
          preview: "Quo-1001",
        },
        {
          module: "Inquiry",
          prefix: "Inq-",
          starting_number: 1001,
          preview: "Inq-1001",
        },
      ],
      snackbar: {
        show: false,
        text: "",
        color: "primary", // or 'error'
      },
      loading: false,
    };
  },

  async mounted() {
    this.loading = true;
    this.company_id = this.$auth.user.company_id;

    await this.getTransactionNumberSeriesData();

    this.loading = false;
  },

  methods: {
    async getTransactionNumberSeriesData() {
      try {
        const { data } = await this.$axios.get(
          `/transaction_number_series?company_id=${this.company_id}`
        );

        if (!data.json.length) {
          return;
        }
        await this.prepareModules(data.json);
      } catch (error) {
        //   this.showSnackbar("Could not load SMTP config.", "error");
      }
    },

    async prepareModules(data) {
      this.modules = await this.$axios.$get(`/get_modules`);

      let d2 = [];

      this.modules.forEach((item1) => {
        let found = false;

        data.forEach((item2) => {
          if (item1.module === item2.module) {
            found = true;
            d2.push({
              module: item1.module,
              prefix: `${
                item1.prefix ||
                item1.module[0].toUpperCase() + item1.module.slice(1, 3) + "-"
              }`,
              starting_number: `${item1.starting_number || 1001}`,
              preview: `${
                item1.prefix ||
                item1.module[0].toUpperCase() + item1.module.slice(1, 3) + "-"
              }${item1.starting_number || 1001}`,
            });
          }
        });

        if (!found) {
          d2.push({
            module: item1.module,
            prefix: `${item1.module.slice(0, 3)}-`,
            starting_number: "1001",
            preview: `${item1.module.slice(0, 3)}-1001`,
          });
        }
      });

      this.modules = d2;
    },
    setItemPreview(item) {
      item.preview = `${item.prefix}${item.starting_number}`;
    },
    async submit() {
      this.loading = true;
      try {
        const payload = {
          company_id: this.company_id,
          json: this.modules,
        };

        await this.$axios.post("/transaction_number_series", payload);
        this.showSnackbar(
          "Transaction number series saved successfully.",
          "primary"
        );
        this.loading = false;
      } catch (err) {
        console.error(err);
        this.showSnackbar("Failed to save transaction number series.", "error");
        this.loading = false;
      }
    },

    showSnackbar(message, color) {
      this.snackbar.text = message;
      this.snackbar.color = color;
      this.snackbar.show = true;
    },
  },
};
</script>

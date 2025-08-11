<template>
  <v-card class="pa-4" flat max-width="800">
    <v-card-title class="text-h6">SMTP Settings</v-card-title>

    <v-card-text>
      <v-form v-model="valid" ref="form" lazy-validation>
        <v-row>
          <v-col>
            <div class="mb-1 font-weight-medium">Mailer</div>
            <v-text-field
              hide-details
              outlined
              v-model="form.mailer"
              required
            />
          </v-col>

          <v-col>
            <div class="mb-1 font-weight-medium">Host</div>
            <v-text-field hide-details outlined v-model="form.host" required />
          </v-col>

          <v-col>
            <div class="mb-1 font-weight-medium">Port</div>
            <v-text-field
              hide-details
              outlined
              v-model="form.port"
              type="number"
              required
            />
          </v-col>

          <v-col>
            <div class="mb-1 font-weight-medium">Encryption</div>
            <v-text-field hide-details outlined v-model="form.encryption" />
          </v-col>
        </v-row>

        <v-row>
          <v-col>
            <div class="mb-1 font-weight-medium">Username</div>
            <v-text-field
              hide-details
              outlined
              v-model="form.username"
              required
            />
          </v-col>

          <v-col>
            <div class="mb-1 font-weight-medium">Password</div>
            <v-text-field
              hide-details
              outlined
              v-model="form.password"
              type="password"
              required
            />
          </v-col>
        </v-row>

        <v-row>
          <v-col>
            <div class="mb-1 font-weight-medium">From Address</div>
            <v-text-field
              hide-details
              outlined
              v-model="form.from_address"
              required
            />
          </v-col>

          <v-col>
            <div class="mb-1 font-weight-medium">From Name</div>
            <v-text-field
              hide-details
              outlined
              v-model="form.from_name"
              required
            />
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>

    <v-card-actions>
      <v-btn color="primary" @click="submit">Submit</v-btn>
    </v-card-actions>

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
      form: {
        mailer: "smtp",
        host: "smtp.gmail.com",
        port: 587,
        username: "your@gmail.com",
        password: "********",
        encryption: "tls",
        from_address: "",
        from_name: "",
      },
      snackbar: {
        show: false,
        text: "",
        color: "primary", // or 'error'
      },
    };
  },

  async mounted() {
    this.company_id = this.$auth.user.company_id;

    try {
      const { data } = await this.$axios.get(
        `/smtp?company_id=${this.company_id}`
      );
      if (data) {
        this.form = { ...this.form, ...data };
      }
    } catch (error) {
      //   this.showSnackbar("Could not load SMTP config.", "error");
    }
  },

  methods: {
    async submit() {
      if (!this.$refs.form.validate()) return;

      try {
        const payload = {
          ...this.form,
        };

        await this.$axios.post("/smtp", payload);
        this.showSnackbar("SMTP settings saved successfully.", "primary");
      } catch (err) {
        console.error(err);
        this.showSnackbar("Failed to save SMTP settings.", "error");
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

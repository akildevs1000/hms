<template>
  <div v-if="can('master')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ msg }}
      </v-snackbar>
    </div>

    <v-card>
      <v-form ref="form" lazy-validation>
        <v-card-text>
          <v-row>
            <v-col>
              <div class="display-1 pa-2">Assign Modules</div>
            </v-col>
            <v-col>
              <div class="display-1 pa-2 text-right">
                <v-btn small class="primary" to="/master/assign_module">
                  <v-icon small>mdi-arrow-left</v-icon>&nbsp;Back
                </v-btn>
              </div>
            </v-col>
          </v-row>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-autocomplete
                  :rules="Rules"
                  v-model="company_id"
                  :items="companies"
                  item-value="id"
                  item-text="company_code"
                  label="Company*"
                ></v-autocomplete>
              </v-col>

              <v-col cols="12" v-for="(pa, idx) in modules" :key="pa.id">
                <v-checkbox
                  :key="pa.id"
                  :value="pa.id"
                  v-model="module_ids"
                  :label="`${pa.name}`"
                >
                </v-checkbox>
              </v-col>

              <v-col cols="12" v-if="errors && errors.module_ids">
                <span class="error--text">{{ errors.module_ids[0] }}</span>
              </v-col>

              <v-col>
                <v-btn
                  v-if="can('assign_module_create')"
                  :loading="loading"
                  dark
                  small
                  color="primary"
                  class="mr-4"
                  @click="save"
                >
                  Submit
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-form>

      <template v-slot:item.action="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
        <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
      </template>
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  layout: "master",

  data: () => ({
    company_id: "",
    module_ids: [],
    modules: [],
    msg: "",
    snackbar: false,
    loading: false,
    Rules: [v => !!v || "This field is required"],
    errors: [],
    companies: []
  }),
  created() {
    this.getCompanies();
    this.getModules();
  },
  methods: {
    can(per) {
      let u = this.$auth.user;
      return u && u.user_type == per;
    },
    getCompanies() {
      this.$axios
        .get("assign-module/nacs")
        .then(({ data }) => {
          this.loading = false;
          this.companies = data;
        })
        .catch(err => console.log(err));
    },
    getModules() {
      this.$axios
        .get("module")
        .then(({ data }) => {
          this.modules = data.data;
        })
        .catch(err => console.log(err));
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },
    save() {
      if (this.$refs.form.validate()) {
        // this.loading = true;
        this.errors = [];
        let payload = {
          company_id: this.company_id,
          module_ids: this.module_ids
        };
        this.$axios.post("assign-module", payload).then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            return;
          }
          this.msg = "Modules has been assigned";
          this.snackbar = true;
          this.loading = false;

          setTimeout(() => this.$router.push("/master/assign_module"), 2000);
        });
      }
    }
  }
};
</script>

<template>
  <div v-if="can(`master`)">
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
                <v-btn small class="primary" to="/master/assign_permission">
                  <v-icon small>mdi-arrow-left</v-icon>&nbsp;Back
                </v-btn>
              </div>
            </v-col>
          </v-row>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-select
                  :rules="Rules"
                  v-model="company_id"
                  :items="companies"
                  item-value="id"
                  item-text="name"
                  label="Company*"
                  readonly
                ></v-select>
              </v-col>

              <v-col cols="12">
                <v-checkbox
                  @change="setAllIds"
                  :label="`Select All`"
                  v-model="just_ids"
                >
                </v-checkbox>
                <v-checkbox
                  v-for="(pa, idx) in modules"
                  :key="idx"
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
                  v-if="can(`assign_permission_edit`)"
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
  layout({ $auth }) {
    let { user_type } = $auth.user;
    if (user_type == "master") {
      return "master";
    } else if (user_type == "employee") {
      return "employee";
    } else if (user_type == "master") {
      return "default";
    }
  },

  data: () => ({
    company_id: "",
    module_ids: [],
    modules: [],
    msg: "",
    snackbar: false,
    just_ids: false,
    Rules: [v => !!v || "This field is required"],
    errors: [],
    companies: []
  }),
  created() {
    this.$axios
      .get("assign-module/" + this.$route.params.id)
      .then(({ data }) => {
        this.company_id = data.company_id;
        this.module_ids = data.module_ids;
      })
      .catch(err => console.log(err));

    this.$axios
      .get("company")
      .then(({ data }) => {
        this.companies = data.data;
      })
      .catch(err => console.log(err));

    this.$axios
      .get("module")
      .then(({ data }) => {
        this.modules = data.data;
      })
      .catch(err => console.log(err));
  },
  methods: {
    setAllIds() {
      this.module_ids = this.just_ids ? this.modules.map(e => e.id) : [];
    },
    can(per) {
      let u = this.$auth.user;
      return u && u.user_type == per;
    },
    save() {
      this.errors = [];
      let payload = {
        company_id: this.company_id,
        module_ids: this.module_ids
      };
      this.$axios
        .put("assign-module/" + this.$route.params.id, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            return;
          }

          this.msg = "Modules has been assigned";
          this.snackbar = true;
          setTimeout(() => this.$router.push("/master/assign_module"), 2000);
        });
    }
  }
};
</script>

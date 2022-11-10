<template>
  <div v-if="can('master')">
    <div v-if="!preloader">
      <v-row class="mt-5">
        <v-col cols="6">
          <h3>Company</h3>
          <div>Dashboard / Company</div>
        </v-col>
        <v-col cols="6">
          <div class="text-right">
            <v-btn
              v-if="can('master')"
              small
              dark
              class="mb-2 primary"
              to="/master/companies/create"
              >Company +</v-btn
            >
          </div>
        </v-col>
      </v-row>
      <v-row>
        <v-col md="3">
          <v-select
            @change="getDataFromApi(endpoint)"
            outlined
            v-model="per_page"
            :items="[50, 100, 500, 1000]"
            dense
            placeholder="Per Page Records"
          ></v-select>
        </v-col>

        <v-col offset-md="6">
          <v-text-field
            outlined
            @input="searchIt"
            v-model="search"
            dense
            placeholder="Search..."
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row v-if="can('master')">
        <v-col md="3" v-for="(item, index) in data" :key="index">
          <v-card style="min-height: 209px">
            <v-card-title>
              <v-spacer></v-spacer>
              <v-icon
                v-if="can(`company_edit`)"
                @click="editItem(item)"
                color="secondary"
                small
                >mdi-pencil</v-icon
              >

              <v-icon
                v-if="can(`master`)"
                @click="deleteItem(item)"
                color="red"
                small
                >mdi-delete</v-icon
              >
            </v-card-title>

            <v-card-text class="text-center" @click="goDetails(item.id)">
              <div>
                <v-img
                  style="height: 125px; width: 50%; margin: 0 auto"
                  :src="item.logo ? item.logo : '/no-image.PNG'"
                >
                </v-img>
              </div>

              <div>
                <b>{{ item.name }}</b>
              </div>

              <div>
                {{ item.location }}
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <v-row>
        <v-col>
          <div color="pt-2" class="text-center">
            <v-btn
              @click="getDataFromApi(prev_page_url)"
              :disabled="prev_page_url ? false : true"
              color="primary"
              small
              elevation="11"
            >
              <v-icon dark>mdi-chevron-double-left </v-icon>
            </v-btn>
            <v-btn
              @click="getDataFromApi(next_page_url)"
              :disabled="next_page_url ? false : true"
              color="primary"
              small
              elevation="11"
            >
              <v-icon dark>mdi-chevron-double-right </v-icon>
            </v-btn>
          </div>
        </v-col>
      </v-row>
    </div>
    <Preloader v-else />
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
    endpoint: "company",
    search: "",
    preloader: true,
    loading: false,
    data: [],
    total: 0,
    next_page_url: "",
    prev_page_url: "",
    current_page: 1,
    per_page: 10
  }),
  async created() {
    this.getDataFromApi();
  },
  methods: {
    can(per) {
      let u = this.$auth.user;
      return u && u.user_type == per;
    },
    goDetails(id) {
      this.$router.push(`/master/companies/details/${id}`);
      // this.$router.push(`/master/companies/${item.id}`);
    },

    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 1) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },
    getDataFromApi(url = this.endpoint) {
      let options = {
        params: {
          per_page: this.per_page
        }
      };

      this.$axios.get(`${url}`, options).then(({ data }) => {
        let { total, next_page_url, prev_page_url, current_page } = data;

        this.data = data.data;
        this.total = total;
        this.next_page_url = next_page_url;
        this.prev_page_url = prev_page_url;
        this.current_page = current_page;
        this.preloader = false;
      });
    },
    editItem(item) {
      this.$router.push(`/master/companies/${item.id}`);
    },
    deleteItem(item) {
      confirm("Are you sure you want to delete this item?") &&
        this.$axios.delete(this.endpoint + "/" + item.id).then(res => {
          const index = this.data.indexOf(item);
          this.data.splice(index, 1);
        });
    }
  }
};
</script>

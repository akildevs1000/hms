<template>
  <div
    v-if="
      can(`settings_rooms_category_access`) &&
      can(`settings_rooms_category_view`)
    "
  >
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <div>
      <div>
        <v-row>
          <v-col md="12" lg="12">
            <v-tabs
              v-model="activeTab"
              :vertical="vertical"
              background-color="background"
              dense
              flat
              dark
              show-arrows
              class="rounded-md"
            >
              <v-spacer></v-spacer>

              <v-tab active-class="active-link"> Menu </v-tab>
              <v-tab active-class="active-link"> Categories </v-tab>
              <v-tab active-class="active-link"> Timing </v-tab>
              <v-tabs-slider color="#1259a7"></v-tabs-slider>
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <client-only>
                      <MenuItemsComponent :key="componentKey" />
                    </client-only>
                  </v-card-text>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <client-only>
                      <MenuCategoriesComponent :key="componentKey" />
                    </client-only>
                  </v-card-text>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <client-only>
                      <MenuTimingsComponent :key="componentKey" />
                    </client-only>
                  </v-card-text>
                </v-card>
              </v-tab-item>
            </v-tabs>
          </v-col>
        </v-row>
      </div>
    </div>
  </div>
  <NoAccess v-else />
</template>
<script>
import MenuCategoriesComponent from "../../../components/hotelCheckin/settings/menucategoriescomponent.vue";
import MenuItemsComponent from "../../../components/hotelCheckin/settings/MenuItemsComponent.vue";
import MenuTimingsComponent from "../../../components/hotelCheckin/settings/MenuTimingsComponent.vue";
export default {
  components: {
    MenuCategoriesComponent,
    MenuItemsComponent,
    MenuTimingsComponent,
  },
  data: () => ({
    upload: {
      name: "",
    },
    previewImage: null,
    componentKey: 0,
    vertical: false,
    activeTab: 0,

    pagination: {
      current: 1,
      total: 0,
      per_page: 50,
    },
    Model: "Room Category",
    options: {},
    endpoint: "get_room_type_list",
    search: "",
    snackbar: false,
    dialog: false,
    roomTypeDialog: false,
    ids: [],
    loading: false,
    total: 0,

    editedIndex: -1,
    editedItem: {
      name: "",
      adult: "",
      child: "",
      baby: "",
      price: "",
      short_description: "",
      description: "",
    },

    defaultItem: {
      name: "",
      adult: "",
      child: "",
      baby: "",
      price: "",
      short_description: "",
      description: "",
    },

    response: "",
    data: [],

    selectedFile: "",
    errors: [],
  }),

  computed: {},

  watch: {},

  created() {},

  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
  },
};
</script>

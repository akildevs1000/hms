<template>
  <v-app>
    <v-navigation-drawer
      v-model="drawer"
      dark
      :mini-variant="miniVariant"
      :clipped="clipped"
      fixed
      app
      class="no_print"
      color="background"
    >
      <v-list v-for="(i, idx) in items" :key="idx" style="padding: 5px 0 0 0px">
        <v-list-item
          :to="i.to"
          router
          v-if="!i.hasChildren"
          :class="!miniVariant || 'pl-2'"
        >
          <v-list-item-icon v-if="i.permission" class="ma-2">
            <v-icon>{{ i.icon }}</v-icon>
          </v-list-item-icon>
          <v-list-item-title v-if="i.permission">
            {{ i.title }}&nbsp;
            <v-badge
              v-if="i.title == 'Orders' && order_count > 0"
              color="primary"
              :content="order_count"
              small
            />
          </v-list-item-title>
        </v-list-item>

        <v-list-item
          v-else
          @click="i.open_menu = !i.open_menu"
          :class="!miniVariant || 'pl-2'"
        >
          <v-list-item-icon v-if="i.permission" class="ma-2">
            <v-icon>{{ i.icon }}</v-icon>
          </v-list-item-icon>
          <v-list-item-title v-if="i.permission">{{
            i.title
          }}</v-list-item-title>
          <v-icon v-if="i.permission" small>{{
            !i.open_menu ? "mdi-chevron-down" : "mdi-chevron-up"
          }}</v-icon>
        </v-list-item>
        <div v-if="i.open_menu">
          <div
            style="margin-left: 50px"
            v-for="(j, jdx) in i.hasChildren"
            :key="jdx"
          >
            <v-list-item v-if="j.permission" style="min-height: 0" :to="j.to">
              <v-list-item-title>{{ j.title }}</v-list-item-title>

              <v-list-item-icon>
                <v-icon :to="i.to">{{ j.icon }}</v-icon>
              </v-list-item-icon>
            </v-list-item>
          </div>
        </div>
      </v-list>
    </v-navigation-drawer>
    <!-- color="#910105 #fd9d00" -->

    <v-app-bar color="primary" dark :clipped-left="clipped" fixed app>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-btn icon @click.stop="miniVariant = !miniVariant">
        <v-icon>mdi-{{ `chevron-${miniVariant ? "right" : "left"}` }}</v-icon>
      </v-btn>
      <v-btn icon @click.stop="clipped = !clipped">
        <v-icon>mdi-application</v-icon>
      </v-btn>
      {{ title }}
      <v-spacer></v-spacer>
      <!-- <v-btn icon @click.stop="rightDrawer = !rightDrawer">
        <v-icon>mdi-menu</v-icon>
      </v-btn> -->
      <v-menu
        nudge-bottom="50"
        transition="scale-transition"
        origin="center center"
        bottom
        left
        min-width="200"
        nudge-left="5"
        nudge-right="5"
        fixed
      >
        <template v-slot:activator="{ on, attrs }">
          <label class="px-2" v-bind="attrs" v-on="on">
            {{ getUser }}
          </label>

          <v-btn icon color="yellow" v-bind="attrs" v-on="on">
            <v-avatar>
              <img :src="`https://via.placeholder.com/40x40?text=${getLogo}`" />
            </v-avatar>
          </v-btn>
        </template>

        <v-list fixed light nav dense>
          <v-list-item-group color="primary">
            <!-- <v-list-item>
              <v-list-item-icon>
                <v-icon>mdi-account-multiple-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="black--text"
                  >Profile</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>

            <v-list-item>
              <v-list-item-icon>
                <v-icon>mdi-cog</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="black--text"
                  >Setting</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item> -->

            <v-list-item @click="logout">
              <v-list-item-icon>
                <v-icon>mdi-logout</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="black--text"
                  >Logout</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </v-app-bar>
    <v-main>
      <v-container>
        <nuxt />
      </v-container>
    </v-main>
    <v-navigation-drawer v-model="rightDrawer" :right="right" temporary fixed>
      <v-list>
        <v-list-item @click.native="right = !right">
          <v-list-item-action>
            <v-icon light> mdi-repeat </v-icon>
          </v-list-item-action>
          <v-list-item-title>Switch drawer (click me)</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
  </v-app>
</template>

<script>
export default {
  mounted() {},
  data() {
    return {
      miniVariant: false,
      right: true,
      rightDrawer: false,

      year: new Date().getFullYear(),
      clipped: false,
      open_menu: [],
      drawer: true,
      fixed: false,
      order_count: "",
      admins: [
        ["Management", "mdi-account-multiple-outline"],
        ["Settings", "mdi-cog-outline"]
      ],
      cruds: [
        ["Create", "mdi-plus-outline"],
        ["Read", "mdi-file-outline"],
        ["Update", "mdi-update"],
        ["Delete", "mdi-delete"]
      ],

      items: [
        {
          icon: "mdi-home",
          title: "Home",
          to: "/master",
          permission: this.can("/")
        },
        {
          icon: "mdi-domain",
          title: "Companies",
          to: "/master/companies",
          permission: this.can("company_access")
        },
        {
          icon: "mdi-apps",
          title: "Module",
          open_menu: false,
          permission: this.can("module_access"),
          hasChildren: [
            {
              icon: "mdi-chart-bubble",
              title: "Module",
              to: "/master/module",
              permission: this.can("module_access")
            },
            {
              icon: "mdi-chart-bubble",
              title: "Assign Modules",
              to: "/master/assign_module",
              permission: this.can("assign_module_access")
            }
          ]
        }

        // {
        //   icon: "mdi-account",
        //   title: "User Management",
        //   open_menu: false,
        //   permission: this.can("user_access"),
        //   hasChildren: [
        //     {
        //       icon: "mdi-account",
        //       title: "Users",
        //       to: "/master/user",
        //       permission: this.can("user_access"),
        //     },
        //     {
        //       icon: "mdi-account",
        //       title: "Roles",
        //       to: "/role",
        //       permission: this.can("role_access"),
        //     },
        //     {
        //       icon: "mdi-lock",
        //       title: "Assign Permissions",
        //       to: "/master/assign_permission",
        //       permission: this.can("assign_permission_access"),
        //     },
        //   ],
        // },
        // {
        //   icon: "mdi-history",
        //   title: "Logs",
        //   to: "/master/attendance_logs",
        //   permission: this.can("/"),
        // },
      ],
      clipped: true,

      miniVariant: false,
      title: "Attendance System",
      logout_btn: {
        icon: "mdi-logout",
        label: "Logout"
      }
    };
  },
  created() {
    this.items = this.items.filter(e => e.permission == true);
  },
  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },
    async logout() {
      await this.$auth.logout();
    }
  },
  computed: {
    getUser() {
      return this.$auth.user && this.$auth.user.name;
    },

    getLogo() {
      return Array.from(
        this.$auth.user && this.$auth.user.name
      )[0].toUpperCase();
    }
  }
};
</script>
<style scoped>
.primary {
  background: #5fafa3 !important;
}
</style>

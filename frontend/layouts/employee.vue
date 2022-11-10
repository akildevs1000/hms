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
          :class="!miniVariant || 'pl-2'"
          v-if="!i.hasChildren"
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
          :class="!miniVariant || 'pl-2'"
          @click="i.open_menu = !i.open_menu"
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
        <div v-if="i.open_menu && i.title == `Modules`">
          <div
            style="margin-left: 50px"
            v-for="(j, jdx) in modules.module_names"
            :key="jdx"
          >
            <v-list-item v-if="j.permission" style="min-height: 0" :to="j.to">
              <v-list-item-title>{{ j.title }}</v-list-item-title>

              <v-list-item-icon>
                <v-icon :to="j.to">{{ j.icon }}</v-icon>
              </v-list-item-icon>
            </v-list-item>
          </div>
        </div>

        <div v-else-if="i.open_menu && i.title != `Modules`">
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
              <img :src="getLogo || '/no-image.PNG'" />
            </v-avatar>
          </v-btn>
        </template>

        <v-list fixed light nav dense>
          <v-list-item-group color="primary">
            <v-list-item>
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
            </v-list-item>

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
    <v-main
      :style="
        $nuxt.$route.path == '/employee_dashboard/announcement'
          ? 'background-color: #ECF0F4'
          : ''
      "
    >
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
    <!-- <v-footer :fixed="fixed" app>
      <v-icon Logout color="primary" @click="logout">{{
        logout_btn.icon
      }}</v-icon>

      <span class="primary--text">Logout</span>

      <v-spacer></v-spacer>
      <span class="primary--text">
        <v-icon v-if="$auth && this.$auth.user.role" Logout color="primary"
          >mdi-account</v-icon
        >
      </span>
    </v-footer> -->
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
          to: "/employee_dashboard",
          permission: this.can("/")
        },
        {
          icon: "mdi-domain",
          title: `Employee`,
          open_menu: false,
          permission: this.can("employee_access"),
          hasChildren: [
            {
              icon: "mdi-domain",
              title: "My Profile",
              to: `/employee_dashboard/profile/${this.$auth?.user?.employee?.id}`, //this.$auth.user.employee.id
              permission: this.can("employee_access")
            },
            {
              icon: "mdi-door",
              title: "Leave Request",
              to: "/employee_dashboard/leave",
              permission: this.can("department_access")
            },

            // {
            //   icon: "mdi-bullhorn-variant-outline",
            //   title: "Announcement",
            //   to: "/announcement",
            //   permission: this.can("employee_access"),
            // },

            {
              icon: "mdi-bullhorn-variant-outline",
              title: "Leave Notification",
              to: "/employee_dashboard/leave_notification",
              permission: this.can("leave_notification_access")
            }
          ]
        },
        {
          icon: "mdi-clipboard-text-clock",
          title: "Reports",
          to: "/attendance_report",
          permission: this.can("attendance_report")
        }

        // {
        //   icon: "mdi-cog",
        //   title: "Setting",
        //   to: "/setting",
        //   permission: this.can("/"),
        // },
      ],
      modules: {
        module_ids: [],
        module_names: []
      },
      clipped: true,

      miniVariant: false,
      title: "ideaHRMS",
      logout_btn: {
        icon: "mdi-logout",
        label: "Logout"
      }
    };
  },
  created() {
    this.getCompanyDetails();
  },
  computed: {
    getUser() {
      return this.$auth.user && this.$auth.user.employee.first_name;
    },

    getLogo() {
      return this.$auth.user && this.$auth.user.employee.profile_picture;
    }
  },
  methods: {
    caps(str) {
      return str.replace(/\b\w/g, c => c.toUpperCase());
    },

    getCompanyDetails() {
      let user = this.$auth.user;

      this.$axios.get(`company/${user?.company?.id}`).then(({ data }) => {
        let { modules } = data.record;

        this.modules = {
          module_ids: modules.module_ids || [],
          module_names: modules.module_names.map(e => ({
            icon: "mdi-chart-bubble",
            title: this.caps(e),
            to: "/" + e + "_modules",
            permission: true
          }))
        };
      });
    },
    can(per) {
      let user = this.$auth.user;
      return user && user.permissions.some(e => e == per || per == "/");
    },
    async logout() {
      this.$axios.get(`/logout`).then(({ res }) => {
        this.$auth.logout();
      });
    }
  }
};
</script>
<style>
.gradient-custom-2 {
  background: #fccb90;
  background: -webkit-linear-gradient(to right, #ee7724, #d8363a);
  background: linear-gradient(to right, #ee7724, #d8363a);
}

/* .page-enter-active,
.page-leave-active {
  transition: opacity 0.5s;
}
.page-enter,
.page-leave-to {
  opacity: 0;
}

.layout-enter-active,
.layout-leave-active {
  transition: opacity 0.5s;
}
.layout-enter,
.layout-leave-to {
  opacity: 0;
}

.slide-bottom-enter-active,
.slide-bottom-leave-active {
  transition: opacity 0.25s ease-in-out, transform 0.25s ease-in-out;
}
.slide-bottom-enter,
.slide-bottom-leave-to {
  opacity: 0;
  transform: translate3d(0, 15px, 0);
}
.bounce-enter-active {
  transform-origin: top;
  animation: bounce-in 0.8s;
}
.bounce-leave-active {
  transform-origin: top;
  animation: bounce-out 0.5s;
}
@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.25);
  }
  100% {
    transform: scale(1);
  }
}
@keyframes bounce-out {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.25);
  }
  100% {
    transform: scale(0);
  }
} */
</style>

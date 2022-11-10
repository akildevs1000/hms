<template>
  <v-app dark>
    <v-list v-for="(link, i) in links" :key="i">
      <v-list-tile
        v-if="!link.subLinks"
        :key="i"
        :to="link.to"
        :active-class="color"
        avatar
        class="v-list-item"
      >
        <v-list-tile-action>
          <v-icon>{{ link.icon }}</v-icon>
        </v-list-tile-action>

        <v-list-tile-title v-text="link.text" />
      </v-list-tile>

      <v-list-group v-else :key="item.title" no-action>
        <template v-slot:activator>
          <v-list-tile>
            <v-list-tile-content>
              <v-list-tile-title>{{ item.title }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </template>

        <v-list-tile v-for="sublink in item.subLinks" :to="sublink.to" :key="sublink.text">
          <v-list-tile-title v-text="sublink.title" />
        </v-list-tile>
      </v-list-group>

  </v-list>
  </v-app>
</template>

<script>
export default {
  data () {
    return {
      links: [
  {
    to: '/dashboard',
    icon: 'mdi-view-dashboard',
    text: 'Dashboard'
  },
  {
    icon: 'mdi-tennis',
    text: 'Players',
    subLinks: [
      {
        text: 'Players list',
        to: '/players'
      },
      {
        text: 'Import WTA Players',
        to: '/players/import'
      }
    ]
  },
  {
    to: '/tournaments',
    icon: 'mdi-trophy',
    text: 'Tournaments'
  }
],
      year: new Date().getFullYear(),
      clipped: false,
      drawer: true,
      fixed: false,
      items: [
        {
          icon: 'mdi-apps',
          title: 'Home',
          to: '/'
        },
        //  {
        //   icon: 'mdi-chart-bubble',
        //   title: 'Customer',
        //   to: '/customer'
        // },
        // {
        //   icon: 'mdi-chart-bubble',
        //   title: 'User',
        //   to: '/user'
        // },
        // {
        //   icon: 'mdi-chart-bubble',
        //   title: 'Role',
        //   to: '/role'
        // },
        //  {
        //   icon: 'mdi-chart-bubble',
        //   title: 'Status',
        //   to: '/status'
        // },
        //  {
        //   icon: 'mdi-chart-bubble',
        //   title: 'Category',
        //   to: '/category'
        // },
        //  {
        //   icon: 'mdi-chart-bubble',
        //   title: 'Sub Category',
        //   to: '/subcategory'
        // },
        //  {
        //   icon: 'mdi-chart-bubble',
        //   title: 'Brand',
        //   to: '/brand'
        // },

        {
          icon: 'mdi-chart-bubble',
          title: 'Products',
          to: '/products'
        },
       
      ],
      miniVariant: false,
      right: true,
      rightDrawer: false,
      title: 'Account Management System',
      logout_btn:{
        icon:'mdi-logout',
        label:'Logout'
      }
    }
  },
  methods:{
  async logout() {
    await this.$auth.logout();
  },
  }
}
</script>

<template>
  <div>

    <v-app v-if="isMobile">

      <!-- <v-system-bar color="deep-purple darken-3"></v-system-bar> -->

      <!-- <v-app-bar color="deep-purple accent-4" dark prominent> -->
      <v-app-bar elevation="0" color="deep-purple darken-3" dark :clipped-left="clipped" fixed app height="-14">

        <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>

        <div style="display: grid">
          <v-toolbar-title class="ma-0 pa-0">Mohamed Fahath</v-toolbar-title>
          <small class="ma-0 pa-0">Reservation : 1003</small>
        </div>

        <v-spacer></v-spacer>
        <v-avatar color="primary">
          <v-img
            src="https://images.unsplash.com/photo-1633332755192-727a05c4013d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8dXNlcnxlbnwwfHwwfHx8MA%3D%3D&w=1000&q=80">
          </v-img>
        </v-avatar>
        <!-- <v-btn icon>
        <v-icon>mdi-magnify</v-icon>
      </v-btn>

      <v-btn icon>
        <v-icon>mdi-filter</v-icon>
      </v-btn>

      <v-btn icon>
        <v-icon>mdi-dots-vertical</v-icon>
      </v-btn> -->
      </v-app-bar>

      <v-navigation-drawer v-model="drawer" absolute temporary>
        <v-list nav dense>
          <v-list-item-group v-model="group" active-class="deep-purple--text text--accent-4">
            <v-list-item>
              <v-list-item-title>Foo</v-list-item-title>
            </v-list-item>

            <v-list-item>
              <v-list-item-title>Bar</v-list-item-title>
            </v-list-item>

            <v-list-item>
              <v-list-item-title>Fizz</v-list-item-title>
            </v-list-item>

            <v-list-item>
              <v-list-item-title>Buzz</v-list-item-title>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-navigation-drawer>

      <v-main class="main_bg">
        <div style="margin-top: 100px;">
          <nuxt />
        </div>
      </v-main>

      <!-- <nuxt class="mt-5" /> -->
      <!--
    <v-card-text>
      <v-container class="mt-5">
      </v-container>
    </v-card-text>
   -->

    </v-app>
    <div v-if="isMobile == false">

    </div>
    <div v-else>

    </div>
  </div>
</template>

<script>
export default {
  data: () => ({
    drawer: false,
    group: null,
    clipped: false,
    isMobile: "",
  }),

  watch: {
    group() {
      this.drawer = false
    },
  },

  mounted() {
    this.checkDeviceType();
    window.addEventListener('resize', this.checkDeviceType);
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.checkDeviceType);
  },
  methods: {
    checkDeviceType() {
      this.isMobile = window.innerWidth <= 768; // Adjust the breakpoint as needed
    },
  },
}
</script>

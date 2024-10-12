<template>
  <div class="pt-2">
    <v-row no-gutters>
      <v-col cols="12" class="mb-5">
        <v-card class="px-2">
          <v-row>
            <v-col v-for="(stat, index) in stats" :key="index">
              <AssetsCard :options="stat" />
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <v-tabs right>
          <v-tab>In House</v-tab>
          <v-tab>Checkout</v-tab>
          <v-tab>Reservation</v-tab>
          <v-tab>All Guest</v-tab>
          <v-tab-item>
            <div v-if="can('in_house_access') && can('in_house_view')">
              <ReservationAllList
                :endpoint="'in_house_reservation_list'"
                :Model="'In House'"
                @response="(e) => (stats = e)"
              /></div
          ></v-tab-item>
          <v-tab-item>
            <div v-if="can('checkout_access') && can('checkout_view')">
              <ReservationAllList
                :endpoint="'check_out_reservation_list'"
                :Model="' Check out Reservation'"
                @response="(e) => (stats = e)"
              />
            </div>
          </v-tab-item>
          <v-tab-item>
            <div v-if="can('reservation_access') && can('reservation_view')">
              <ReservationAllList
                :endpoint="'up_coming_reservation_list'"
                :Model="'Reservation'"
                @response="(e) => (stats = e)"
              />
            </div>
          </v-tab-item>
          <v-tab-item>
            <div v-if="can('reservation_access') && can('reservation_view')">
              <ReservationAllList
                :endpoint="'all_reservation_list'"
                :Model="'All Guest'"
                @response="(e) => (stats = e)"
              />
            </div>
          </v-tab-item>
        </v-tabs>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  data: () => ({
    stats: [],
  }),
  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
  },
};
</script>

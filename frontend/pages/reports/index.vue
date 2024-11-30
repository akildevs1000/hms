<template>
  <v-card v-if="can(`analytics_access`)">
    <v-container fluid>
      <Report />
    </v-container>
  </v-card>
  <NoAccess v-else />
</template>

<script>
export default {
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

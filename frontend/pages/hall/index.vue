<template>
  <div v-if="can(`hall_access`)">
    <v-tabs right>
      <v-tab>Hall</v-tab>
      <v-tab>Category</v-tab>
      <v-tab-item>
        <Hall v-if="can(`hall_access`)" />
        <NoAccess v-else
      /></v-tab-item>
      <v-tab-item>
        <HallCategory v-if="can(`hall_access`)" />
        <NoAccess v-else
      /></v-tab-item>
    </v-tabs>
  </div>
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

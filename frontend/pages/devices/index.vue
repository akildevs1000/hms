<template>
  <div v-if="can('devices_permissions_access') && can('devices_view')">
    <Deviceslist :addNew="true"></Deviceslist>
  </div>
  <NoAccess v-else />
</template>
<script>
import Deviceslist from "../../components/devices/deviceslist.vue";

export default {
  data: () => ({}),
  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
  },
  components: { Deviceslist },
};
</script>

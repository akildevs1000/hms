<template>
  <div v-if="can('company_access')">
    <v-tabs right dense>
      <v-tab>Whatsapp</v-tab>
      <v-tab>Email</v-tab>
      <v-tab-item>
        <WhatsappTemplate medium="whatsapp" />
      </v-tab-item>
      <v-tab-item>
        <EmailTemplate medium="email" />
      </v-tab-item>
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

<template>
  <div>
    <v-row class="mt-10">
      <v-col :cols="col" v-for="card in cards" :key="card.id">
        <v-card :to="'report/' + card.link" class="no_print">
          <v-list-item>
            <v-list-item-content>
              <div class="overline mb-4">Module</div>
              <v-list-item-title color="pink darken-2" class=" title mb-1">{{
                card.name
              }}</v-list-item-title>
              <v-list-item-subtitle> </v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-avatar tile class="primary" size="60">
              <span>
                <v-icon class="white--text">{{ card.icon }}</v-icon>
              </span>
            </v-list-item-avatar>
          </v-list-item>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  data: () => ({
    col: 4,
    cards: [
      { id: 1, name: "Payroll", icon: "mdi-trending-up", link: "sales" },
      { id: 2, name: "Hr Management", icon: "mdi-account", link: "sales" },
      { id: 3, name: "Attendance Management", icon: "mdi-account", link: "sales" },     
    ]
  })
};
</script>

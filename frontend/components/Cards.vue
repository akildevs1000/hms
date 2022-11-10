<template>
  <v-row>
    <v-col :md="columns" v-for="(i, index) in cards" :key="index">
      <v-card :to="i.link" class="no_print">
        <v-list-item three-line>
          <v-list-item-content>
            <div class="text-overline mb-4">
              {{ i.subheading }}
            </div>
            <v-list-item-title class="text-h5 mb-1">
              {{ i.heading }}
            </v-list-item-title>
          </v-list-item-content>

          <v-list-item-avatar size="60" class="primary">
            <v-icon dark>{{ i.icon }}</v-icon>
          </v-list-item-avatar>
        </v-list-item>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: ["cards", "columns"]
};
</script>

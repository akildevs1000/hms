<template>
  <span>
    <v-card flat v-for="(item, index) in items" :key="index" class="mb-2" dense>
      <v-row no-gutters>
        <v-col>
          <v-btn class="text-color" text @click="toggleExpand(index)">
            <div class="text-color">
              {{ item.title }}
            </div>
          </v-btn>
        </v-col>
        <v-col class="text-right">
          <v-icon color="text-color" small text @click="toggleExpand(index)" class="mt-2">
            mdi-chevron-down
          </v-icon>
        </v-col>
      </v-row>
      <v-expand-transition>
        <v-card-text v-if="expandedIndex === index">
          {{ item.content }}
        </v-card-text>
      </v-expand-transition>
      <v-divider></v-divider>
    </v-card>
  </span>
</template>

<script>
export default {
  data() {
    return {
      expandedIndex: null, // Index of the currently expanded item
      items: [
        { title: "Item 1", content: "Content for Item 1" },
        { title: "Item 2", content: "Content for Item 2" },
        { title: "Item 3", content: "Content for Item 3" },
      ],
    };
  },
  methods: {
    toggleExpand(index) {
      // If the index is already expanded, collapse it; otherwise, expand it
      this.expandedIndex = this.expandedIndex === index ? null : index;
    },
  },
};
</script>

<template>
  <v-container>
    <v-row>
      <v-col>
        <!-- <pre>{{permissions}}</pre> -->
        <v-treeview class="text-color"
          v-model="selection"
          :items="items"
          :selection-type="selectionType"
          selectable
          return-object
        ></v-treeview>
      </v-col>
      <!-- <v-divider vertical></v-divider>
      <v-col class="pa-6" cols="6">
        <template v-if="!selection.length"> No nodes selected. </template>
        <template v-else>
          <div v-for="(node,index) in permissions" :key="index">
            {{ index }}
          </div>
        </template>
      </v-col> -->
    </v-row>
  </v-container>
</template>
<script>
export default {
  props: ["permissions"],
  data: () => ({
    selectionType: "leaf",
    selection: [],
    items: [
      {
        id: 1,
        name: "Dashbaord",
        children: [
          {
            id: 8,
            name: "Home",
            children: [
              { id: 9, name: "Access" },
              { id: 10, name: "View" },
              { id: 11, name: "Create" },
              { id: 12, name: "Edit" },
              { id: 13, name: "Delete" },
            ],
          },
          {
            id: 14,
            name: "Calendar",
            children: [
              { id: 15, name: "Access" },
              { id: 16, name: "View" },
              { id: 17, name: "Create" },
              { id: 18, name: "Edit" },
              { id: 19, name: "Delete" },
            ],
          },
          {
            id: 21,
            name: "History",
            children: [
              { id: 22, name: "Access" },
              { id: 23, name: "View" },
              { id: 24, name: "Create" },
              { id: 25, name: "Edit" },
              { id: 26, name: "Delete" },
            ],
          },
          {
            id: 27,
            name: "Night Audit",
            children: [
              { id: 28, name: "Access" },
              { id: 29, name: "View" },
              { id: 30, name: "Create" },
              { id: 31, name: "Edit" },
              { id: 32, name: "Delete" },
            ],
          },
          {
            id: 33,
            name: "Lost & Found",
            children: [
              { id: 34, name: "Access" },
              { id: 35, name: "View" },
              { id: 36, name: "Create" },
              { id: 37, name: "Edit" },
              { id: 38, name: "Delete" },
            ],
          },
        ],
      },
      {
        id: 2,
        name: "Customer",
        children: [
          { id: 2, name: "Child #1" },
          { id: 3, name: "Child #2" },
          {
            id: 4,
            name: "Child #3",
            children: [
              { id: 5, name: "Grandchild #1" },
              { id: 6, name: "Grandchild #2" },
            ],
          },
        ],
      },
      {
        id: 3,
        name: "Accounts",
        children: [
          { id: 2, name: "Child #1" },
          { id: 3, name: "Child #2" },
          {
            id: 4,
            name: "Child #3",
            children: [
              { id: 5, name: "Grandchild #1" },
              { id: 6, name: "Grandchild #2" },
            ],
          },
        ],
      },
      {
        id: 4,
        name: "Sales",
        children: [
          { id: 2, name: "Child #1" },
          { id: 3, name: "Child #2" },
          {
            id: 4,
            name: "Child #3",
            children: [
              { id: 5, name: "Grandchild #1" },
              { id: 6, name: "Grandchild #2" },
            ],
          },
        ],
      },
      {
        id: 5,
        name: "Reports",
        children: [
          { id: 2, name: "Child #1" },
          { id: 3, name: "Child #2" },
          {
            id: 4,
            name: "Child #3",
            children: [
              { id: 5, name: "Grandchild #1" },
              { id: 6, name: "Grandchild #2" },
            ],
          },
        ],
      },
      {
        id: 5,
        name: "Settings",
        children: [
          { id: 2, name: "Child #1" },
          { id: 3, name: "Child #2" },
          {
            id: 4,
            name: "Child #3",
            children: [
              { id: 5, name: "Grandchild #1" },
              { id: 6, name: "Grandchild #2" },
            ],
          },
        ],
      },
    ],
  }),
};
</script>

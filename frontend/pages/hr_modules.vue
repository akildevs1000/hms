<template>
  <div>
    <Cards class="mt-2" :cards="cards" :columns="6" />
  </div>
</template>
<script>
export default {
  data: () => ({
    cards: [
      { id: 1, name: "Payroll", icon: "mdi-trending-up", link: "sales" },
      { id: 2, name: "Hr Management", icon: "mdi-account", link: "sales" },
      { id: 3, name: "Attendance Management", icon: "mdi-account", link: "sales" },
    ]
  })
};
</script>

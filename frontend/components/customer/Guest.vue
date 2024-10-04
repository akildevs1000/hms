<template>
  <span>
    <AssetsTable height="300" :headers="headers" :items="sub_customers">
      <template #full_name="{ item }">
        <v-avatar size="40" class="mx-2">
          <v-img
            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRudDbHeW2OobhX8E9fAY-ctpUAHeTNWfaqJA&usqp=CAU"
          ></v-img>
        </v-avatar>
        {{ `${item.title} ${item.first_name} ${item.last_name}` }}
      </template>
      <template #address="{ item }">
       
        {{ item.address || "----" }}
      </template>
    </AssetsTable>
  </span>
</template>
<script>
export default {
  props: ["sub_customers", "booking"],
  data: () => ({
    headers: [
      {
        text: `Full Name`,
        value: `full_name`,
        align: `left`,
        width: "250px",
      },
      { text: `Email`, value: `email`, align: `left` },
      { text: `Contact`, value: `contact_no`, align: `left` },
      { text: `Whatsapp`, value: `whatsapp`, align: `left` },
      { text: `Nationality`, value: `nationality`, align: `left` },
      { text: `Address`, value: `address`, align: `center`, width: "30px" },
    ],
  }),
  created() {},
};
</script>

<template>
  <v-card class="pa-5" outlined>
    <v-row>
      <!-- Left Side Logo and Info -->
      <v-col cols="8">
        <v-img
          src="https://backend.ezhms.com/upload/app-logo.jpeg"
          max-width="100"
          class="mb-3"
        ></v-img>
        <div style="display: block">
          <h4><AssetsTextLabel label="To" /></h4>
          <AssetsTextLabel label="SMB Design & Decor" />
          <br />
          <AssetsTextLabel label="Dubai" />
          <AssetsTextLabel label="TRN 100437109000003" />
        </div>
      </v-col>

      <!-- Right Side Company Info and Statement -->
      <v-col cols="4" class="text-right">
        <h4>
          <AssetsTextLabel
            color="black"
            label="Akil Security & Alarm Systems LLC"
          />
        </h4>
        <AssetsTextLabel label="Dubai P.O.Box : 83481" />
        <br />
        <AssetsTextLabel label="United Arab Emirates" />
        <br />
        <AssetsTextLabel label="TRN 100391471000003" />
        <br />

        <h4 class="mt-5">
          <AssetsTextLabel color="black" label="Statement of Accounts" />
        </h4>

        <span
          class=""
          style="border-top: 1px solid #ddd; border-bottom: 1px solid #ddd"
        >
          <AssetsTextLabel label="01 Jan 2024 to 31 Dec 2024" />
        </span>

        <v-row class="pb-5 pt-2">
          <v-col></v-col>
          <v-col cols="10">
            <table style="width: 100%" cellspacing="0">
              <tr>
                <td colspan="2" class="text-left px-1 grey lighten-3">
                  <h4>
                    <AssetsTextLabel color="black" label="Account Summary" />
                  </h4>
                </td>
              </tr>
              <tr>
                <td class="text-left">
                  <AssetsTextLabel label="Opening Balance" />
                </td>
                <td><AssetsTextLabel label="AED 3,659.25" /></td>
              </tr>
              <tr>
                <td class="text-left">
                  <AssetsTextLabel label="Invoiced Amount" />
                </td>
                <td>
                  <AssetsTextLabel label="AED 6,851.25" />
                </td>
              </tr>
              <tr>
                <td class="text-left">
                  <AssetsTextLabel label="Amount Received" />
                </td>
                <td>
                  <AssetsTextLabel label="AED 8,526.00" />
                </td>
              </tr>
              <tr>
                <td class="text-left" style="border-top: 1px solid #ddd">
                  <AssetsTextLabel label="Balance Due" />
                </td>
                <td style="border-top: 1px solid #ddd">
                  <AssetsTextLabel label="AED 1,984.50" />
                </td>
              </tr>
            </table>
          </v-col>
        </v-row>
      </v-col>
    </v-row>

    <v-divider></v-divider>

    <!-- Transaction Table Section -->

    <AssetsTable :headers="headers" :items="items" />
   

    <!-- <v-row justify="end">
      <v-col cols="3">
        <h4>Balance</h4>
        <p>RS 6,566.00</p>
      </v-col>
    </v-row> -->
  </v-card>
</template>

<script>
export default {
  data() {
    return {
      headers: [],
      items: [],
      data: [
        {
          date: "2024-09-01",
          transaction: "Check-in",
          detail: "Guest: John Doe, Room: 101",
          amount: 150,
          balance: 150,
        },
        {
          date: "2024-09-03",
          transaction: "Room Service",
          detail: "Dinner Order - Room 101",
          amount: -45,
          balance: 105,
        },
        {
          date: "2024-09-05",
          transaction: "Check-out",
          detail: "Guest: John Doe, Room: 101",
          amount: -150,
          balance: 0,
        },
        {
          date: "2024-09-10",
          transaction: "Check-in",
          detail: "Guest: Jane Smith, Room: 202",
          amount: 200,
          balance: 200,
        },
        {
          date: "2024-09-12",
          transaction: "Spa Service",
          detail: "Massage - Room 202",
          amount: -75,
          balance: 125,
        },
        {
          date: "2024-09-15",
          transaction: "Check-out",
          detail: "Guest: Jane Smith, Room: 202",
          amount: -200,
          balance: -75,
        },
        {
          date: "2024-09-20",
          transaction: "Check-in",
          detail: "Guest: Mark Brown, Room: 303",
          amount: 180,
          balance: 180,
        },
        {
          date: "2024-09-22",
          transaction: "Room Service",
          detail: "Breakfast Order - Room 303",
          amount: -30,
          balance: 150,
        },
        {
          date: "2024-09-25",
          transaction: "Check-out",
          detail: "Guest: Mark Brown, Room: 303",
          amount: -180,
          balance: -30,
        },
      ],
    };
  },
  created() {
    this.headers = [
      { text: `#`, value: `serial`, align: `center` },
      { text: `Date`, value: `date`, align: `center` },
      { text: `Transaction`, value: `transaction`, align: `center` },
      { text: `Detail`, value: `detail`, align: `left` },
      { text: `Amount`, value: `amount`, align: `left` },
      { text: `Balance`, value: `balance`, align: `left` },
    ];

    this.items = this.data.map((e, i) => ({
      serial: i + 1,
      date: e.date,
      transaction: e.transaction,
      detail: e.detail,
      amount: this.$utils.currency_format(e.amount),
      balance: this.$utils.currency_format(e.balance),
    }));
  },
};
</script>

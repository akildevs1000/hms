<template>
  <v-dialog v-model="dialog" width="800">
    <AssetsIconClose :left="790" @click="dialog = false" />
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-cash-multiple </v-icon>
      </div>
    </template>
    <style scoped>
      textarea:focus {
        border-color: #42a5f5; /* Replace with your preferred focus color */
        outline: none; /* Remove default outline */
      }
    </style>

    <div class="grey lighten-3 pa-2" style="overflow: hidden">
      <v-card>
        <v-card-text>
          <table style="width: 100%">
            <tr>
              <td width="33%">
                <img :src="company && company.logo" style="width: 100px" />
              </td>
              <td width="33%"></td>
              <td width="33%" class="text-right">
                <table style="width: 100%">
                  <tr>
                    <td>
                      <b>{{ company.name }}</b>
                    </td>
                  </tr>
                  <tr>
                    <td>242/7 Thirivalluval Nager</td>
                  </tr>
                  <tr>
                    <td>Tel : 04362 646411,</td>
                  </tr>
                  <tr>
                    <td>{{ company.location }}</td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td colspan="3" class="text-center">
                <b><Heading label="PAYMENT VOUCHER" color="#ddd"></Heading></b>
              </td>
            </tr>
            <tr>
              <td width="33%" class="text-center">
                <div style="width: 100px; height: 20px; padding: 5px">
                  <v-text-field
                    readonly
                    label="Rs"
                    v-model="item.amount"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </div>
              </td>
              <td></td>
              <td class="pl-15">
                <table style="width: 100%">
                  <tr>
                    <td class="blue--text">Voucher</td>
                    <td class="blue--text">: VN {{ item.id }}</td>
                  </tr>
                  <tr>
                    <td class="red--text">Customer Invoice #</td>
                    <td class="red--text">: VN {{ item.bill_number }}</td>
                  </tr>
                  <tr>
                    <td>Date</td>
                    <td>: {{ item.payment_date }}</td>
                  </tr>
                  <tr>
                    <td>Category</td>
                    <td>: {{ vendor.vendor_category?.name }}</td>
                  </tr>
                  <tr>
                    <td>Prepared By</td>
                    <td>: {{ $auth.user.name }}</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <br />
          <table style="width: 100%">
            <tr>
              <td width="25%" class="py-1 border-top border-bottom">
                <b
                  >{{
                    `${vendor?.title} ${vendor?.first_name} ${vendor?.last_name}`
                  }}
                </b>
              </td>
              <td width="25%" class="py-1 border-top border-bottom">
                <b> Mobile: {{ vendor.mobile }}</b>
              </td>
              <td width="25%" class="py-1 border-top border-bottom">
                <b>{{ vendor.company_name }}</b>
              </td>
              <td width="25%" class="py-1 border-top border-bottom">
                <b>{{
                  `${vendor?.state}, ${vendor?.city} ${vendor?.country} - ${vendor?.zip_code}`
                }}</b>
              </td>
            </tr>
          </table>
          <table style="width: 100%">
            <tr>
              <td width="50%" class="py-1 border-bottom">
                Rs {{ $utils.currency_format(item.amount) }} ( Two Thousand Five
                Hindered Only )
              </td>
              <td class="py-1 border-bottom text-center">
                Payment by : {{ item.payment_mode }}
              </td>
              <td colspan="2" class="py-1 border-bottom text-center">
                Reference : {{ item.payment_mode_ref || "----" }}
              </td>
            </tr>
          </table>
          <table>
            <tr style="width: 100%">
              <td colspan="4" class="py-1 border-bottom">
                <strong>Note:</strong> {{ item.note }}
              </td>
            </tr>
          </table>
          <br />
          <br />

          <v-row>
            <v-col cols="4" class="text-center">
              <v-divider></v-divider>
              <span>Receiver Signature</span>
            </v-col>
            <v-col cols="4"></v-col>
            <v-col cols="4" class="text-center">
              <v-divider></v-divider>
              <span>Manager Signature</span>
            </v-col>
            <v-col cols="12" class="text-center">
              <AssetsButton
                :options="{
                  label: 'Print',
                  color: 'blue',
                  icon: 'printer-outline',
                }"
                @click="printVoucher"
              />
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </div>
  </v-dialog>
</template>
<script>
export default {
  props: ["item", "endpoint", "model", "vendor"],
  data: () => ({
    dialog: false,
  }),
  computed: {
    company() {
      return this.$auth.user.company;
    },
  },

  methods: {
    printVoucher(id) {
      window.open(
        `https://hms-backend.test/api/payment-voucher/${this.item.id}?payee=${this.$auth.user.name}`
      );
    },
    close() {
      this.dialog = false;
    },
  },
};
</script>
